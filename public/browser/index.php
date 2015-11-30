<?php 

$debug=false; //set to true to see solr queries in output
$queryLogger = array();

function __autoload($class_name) {
  $file = dirname($_SERVER["SCRIPT_FILENAME"]). DIRECTORY_SEPARATOR .".." . DIRECTORY_SEPARATOR ."..".DIRECTORY_SEPARATOR."browser".DIRECTORY_SEPARATOR.$class_name.".php";
  if(file_exists($file)) {
    require_once($file);
  } else {
    trigger_error("Class ".$class_name." does not exist");
  }
}

// Read Database access data from application configuration...
$dbConfig = DbConfig::getInstance();
$dbHost = $dbConfig->getHost();
$dbUserName = $dbConfig->getUserName();
$dbPassword = $dbConfig->getPassword();
$dbDbname = $dbConfig->getDbName();
// Read SOLR access data from application configuration...
$solrConfig = SolrConfig::getInstance();
$solrHost = $solrConfig->getHost();
$solrPort = $solrConfig->getPort();
$solrContext = $solrConfig->getContext();
$solrUrl = "http://" . $solrHost . ":" . $solrPort . "/" . $solrContext . "/";

$getData = GetData::getInstance();
$collections = $getData->getCollections($dbHost, $dbUserName, $dbPassword, $dbDbname);
//$users = $getData->getUsers($dbHost, $dbUserName, $dbPassword, $dbDbname);
$tenants = $getData->getTenants($dbHost, $dbUserName, $dbPassword, $dbDbname);
$conceptSchemes = getConceptSchemes($solrUrl);
$skosCollections = getSkosCollections($solrUrl);
$topConceptsConceptScheme = array();
$topConceptsSkosCollection = array();

/// IMPORTANT: The ALL (Any) option for a radio button facet should always be declared last in the config
// Dynamic facets are facets which values can be set at runtime (depending on server search results)
$config = array(
    "searchfields" => array(
      //array("field" => array("definition"), "description"=>"Title"),
      //array("field" => array("DocumentationProperties"), "description"=>"Description"),
      //array("field" => array("DocumentationPropertiesText", "LexicalLabelsPhrase"), "description"=>"Default Textual Fields") // same as in the Concept Registry OpenSkos search application
      array("field" => array("LexicalLabelsText"), "description"=>"Labels"),
      array("field" => array("definitionText"), "description"=>"Definition"),
      array("field" => array("DocumentationProperties", "DocumentationPropertiesText"), "description"=>"Default documentation fields") // same as in the Concept Registry OpenSkos search application
    ),
      "facetfields"=> array(
//         array("title" => "Object type", "type" => "radio", "facet"=>"class", "values" => array(
//             array("value"=>"SKOSCollection", "description" => "Skos Collection"),
//             array("value"=>"ConceptScheme", "description" => "Concept Scheme"),
//             array("value"=>"Concept", "description" => "Concept"),
//             array("value"=>"ALL", "description" => "Any")
//           )
//         ),
      	  array("title" => "Status", "type" => "radio", "facet"=>"status", "values" => array(
              array("value"=>"approved", "description" => "Approved"),
              array("value"=>"candidate", "description" => "Candidate"),
              array("value"=>"ALL", "description" => "Any")
            )
          ),
      		array("title" => "Concept Schemes", "type" => "checkbox", "facet"=>"inScheme", "values" => $conceptSchemes),
      		array("title" => "Collections", "type" => "checkbox", "facet"=>"inSkosCollection", "values" => $skosCollections),
//         array("title" => "Approved by", "type" => "radio", "facet"=>"approved_by", "dynamic"=>true, "values" => array( //  dynamic facet values based on "facet" name
//             array("value"=>"ALL", "description" => "Any")
//           )
//         ),
//         array("title" => "User", "type" => "checkbox", "facet"=>"created_by", "values" => array(
//             array("value"=>"0", "description" => "Created by user 0"),
//             array("value"=>"1", "description" => "Created by user 1")
//           )
//         ),
        array("title" => "Tenant's Collections", "type" => "radio", "facet"=>"collection", "dynamic"=>true, "values" => array( //  dynamic facet values based on "facet" name
            array("value"=>"ALL", "description" => "Any")
          )
        ),
        array("title" => "Tenants", "type" => "radio", "facet"=>"tenant", "dynamic"=>true, "values" => array( 
            array("value"=>"ALL", "description" => "Any")
      	  )
        ),      		
    )      
);



// read url params..
$start = isset($_GET["start"])?intval($_GET["start"]):0;
$number = isset($_GET["number"])?max(1,intval($_GET["number"])):10;
$key = (isset($_GET["key"])&&$_GET["key"])?$_GET["key"]:"";
$matchTermsExact = isset($_GET["matchTermsExact"])?$_GET["matchTermsExact"]:false;
$termsOr = isset($_GET["termsOr"])?$_GET["termsOr"]:false;

// refresh search options configuration state based on the params in the url...
$urlFacetField = "";
$urlSearchField = "";
for($i=0;$i<count($config["searchfields"]);$i++) {
  if(isset($_GET["field".$i])&&(trim($_GET["field".$i])!="")) {  
    $tmp_searchfieldSelected = true;
    $config["searchfields"][$i]["checked"] = true;
    // add all fields in the array to the url..
    $urlSearchField.="&field".$i."=".urlencode(implode(",", $config["searchfields"][$i]["field"]));
  } else {
    $config["searchfields"][$i]["checked"] = false;
  }
}
for($i=0;$i<count($config["facetfields"]);$i++) {  
  //checkbox type
  if($config["facetfields"][$i]["type"]=="checkbox") {
    for($j=0;$j<count($config["facetfields"][$i]["values"]); $j++) {
      if(isset($_GET["facet".$i."_".$j])&&(trim($_GET["facet".$i."_".$j])!="")) {
        $config["facetfields"][$i]["values"][$j]["checked"] = true;
        $urlFacetField.="&facet".$i."_".$j."=".urlencode($config["facetfields"][$i]["values"][$j]["value"]);
      } else {
        $config["facetfields"][$i]["values"][$j]["checked"] = false;
      }
    }
  }
  // radio type
  else if($config["facetfields"][$i]["type"]=="radio") {
    $config["facetfields"][$i]["value"] = "";
    for($j=0;$j<count($config["facetfields"][$i]["values"]); $j++) {
      if(isset($_GET["facet".$i])&&($_GET["facet".$i]==$config["facetfields"][$i]["values"][$j]["value"])) {
        $config["facetfields"][$i]["value"] = $config["facetfields"][$i]["values"][$j]["value"];
        $config["facetfields"][$i]["values"][$j]["checked"] = true;
        $urlFacetField.="&facet".$i."=".urlencode($config["facetfields"][$i]["values"][$j]["value"]);
      } else {
        $config["facetfields"][$i]["values"][$j]["checked"] = false;
      }
    }
    //if empty select last option 
    if($config["facetfields"][$i]["value"]=="") {
      $config["facetfields"][$i]["value"] = $config["facetfields"][$i]["values"][count($config["facetfields"][$i]["values"]) - 1]["value"];
      $config["facetfields"][$i]["values"][count($config["facetfields"][$i]["values"]) - 1]["checked"] = true;
    }
  }
}

// update the DYNAMIC facets config with the DYNAMIC facets data..
for($i=0;$i<count($config["facetfields"]);$i++) {
  if((isset($config["facetfields"][$i]["dynamic"]) && $config["facetfields"][$i]["dynamic"])) { // dynamic facet..
    if ($config["facetfields"][$i]["facet"] == "collection") {
      foreach($collections as $collectionID => $collectionCode) {
        $config["facetfields"][$i]["values"][] = array("value" => $collectionID, "description" => $collectionCode);
      }
      $config["facetfields"][$i]["values"][] = array_shift($config["facetfields"][$i]["values"]); // push "ANY" to the tail of the array...
    }
    else if ($config["facetfields"][$i]["facet"] == "tenant") {
      foreach($tenants as $tenantCode => $tenantName) {
        $config["facetfields"][$i]["values"][] = array("value" => $tenantCode, "description" => $tenantName);
      }
      $config["facetfields"][$i]["values"][] = array_shift($config["facetfields"][$i]["values"]); // push "ANY" to the tail of the array...
    }
//     else if ($config["facetfields"][$i]["facet"] == "approved_by") {
//       foreach($users as $userID => $userName) {
//         $config["facetfields"][$i]["values"][] = array("value" => $userID, "description" => $userName);
//       }
//       $config["facetfields"][$i]["values"][] = array_shift($config["facetfields"][$i]["values"]); // push "ANY" to the tail of the array...
//     }
    else if ($config["facetfields"][$i]["facet"] == "inSkosCollection") {
      foreach($skosCollections as $item) {
        $config["facetfields"][$i]["values"][] = array("value" => $item['uri'], "description" => $item['dcterms_title'][0]);
      }
      $config["facetfields"][$i]["values"][] = array_shift($config["facetfields"][$i]["values"]); // push "ANY" to the tail of the array...
    }
    else {
      trigger_error("Unknown dynamic facet type encountered: " . $config["facetfields"][$i]["facet"]);
      die();
    }
    // handle state parameter in url...
    $foundACheckedOne = false;
    for($j=0;$j<count($config["facetfields"][$i]["values"]); $j++) {
      if(isset($_GET["facet".$i])&&($_GET["facet".$i]==$config["facetfields"][$i]["values"][$j]["value"])) {
        $config["facetfields"][$i]["value"] = $config["facetfields"][$i]["values"][$j]["value"];
        $config["facetfields"][$i]["values"][$j]["checked"] = true;
        $foundACheckedOne = true;
      } else {
        $config["facetfields"][$i]["values"][$j]["checked"] = false;
      }
    }
    if ($foundACheckedOne == false) { // didn't find a checked one..nothing was set by URL yet.. set last as checked
      $config["facetfields"][$i]["values"][count($config["facetfields"][$i]["values"]) - 1]["checked"] = true;
    }
  }
}

// initial solr search call...
list($list,$total, $facetFieldCounts, $facetQueryCounts) = getList($start,$number,$config,$solrUrl);


?><html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <title>ACDH Vocabularies Browser</title>
  <link href="css/skos.css" rel="stylesheet" type="text/css" />
  <script src="../scripts/jquery-1.11.1.min.js"></script>
  <meta name="author" content="Martin Snijders & Rob Zeeman">
  <script type="text/javascript">
    $(document).ready(function() {        
      $("a.clearFields").click(function() 
        { 
        $("form#formulier input.checkboxField").prop("checked", false);
        $("form#formulier").submit();
        return false;
      });
      $("form#formulier input.checkboxField").click(function(){ $("form#formulier").submit(); });
      $("a.clearFacets").click(function() { 
        $("form#formulier input.checkboxFacet").prop("checked", false);
        $("form#formulier input.radioFacet").prop("checked", false);
        $("form#formulier").submit();
        return false;
      });
      $("form#formulier input.checkboxFacet, form#formulier input.radioFacet").click(function(){ $("form#formulier").submit(); });
      $("form#formulier input.termsOr").click(function(){ $("form#formulier").submit(); });
      $("form#formulier input.matchTermsExact").click(function(){ $("form#formulier").submit(); });
    });          
  </script>
  <!-- Piwik -->
  <script type="text/javascript">
    var _paq = _paq || [];
    _paq.push(['trackPageView']);
    _paq.push(['enableLinkTracking']);
    (function() {
      var u="//clarin.oeaw.ac.at/piwik/";
      _paq.push(['setTrackerUrl', u+'piwik.php']);
      _paq.push(['setSiteId', 13]);
      var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
      g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
    })();
  </script>
<noscript><p><img src="//clarin.oeaw.ac.at/piwik/piwik.php?idsite=13" style="border:0;" alt="" /></p></noscript>
<!-- End Piwik Code -->

</head>
<body>
<div id="wrap">
  <div id="header">
   <img id="logo" name="logo" alt="CLARIN" src="img/bg.png">
   <a href="/vocabs/">
     <h1>ACDH Vocabularies Browser</h1>
   </a>
  </div>
  <?php if($debug) {?><div><h3>Debug-modus</h3>
    <ul>
      <?php
      foreach($queryLogger AS $queryLoggerItem) {
      	echo("<li><pre style=\"white-space: pre-wrap;\"><a target=\"_blank\" href=\"".htmlEscape($solrUrl."/select?".$queryLoggerItem)."\">".htmlEscape($queryLoggerItem)."</a></pre></li>");
      } 
      ?>      
    </ul>
  </div><?php } ?>
  <form id="formulier" action="" method="GET">
    <p>This is the browser view of the <a href="https://clarin.oeaw.ac.at/vocabs">vocabulary service</a> based on the software <a href="http://openskos.org">OpenSKOS</a> run by <a href="http://www.oeaw.ac.at/acdh">ACDH-OEAW</a> as a service for the research community in the context of research infrastructures <a href="http://clarin.eu" >CLARIN-ERIC</a> and <a href="http://dariah.eu">DARIAH-EU</a>.</p>
    <p>
      <strong>Please type one or more space separated search terms</strong>
    </p>
    </strong>
    <input class="text" type="text" size="25" name="key"
      value="<?php if(isset($_GET["key"])&&$_GET["key"]) { echo(htmlEscape($_GET["key"],NULL, "UTF-8"));} ?>">
    <input class="button" type="submit" value="Search">
    <input class="button" type="button" onclick="location.href='<?php echo(htmlEscape($_SERVER["PHP_SELF"]));?>';" value="Reset all"><br />
    <br />
    <table class="list">
      <tr>
        <td>
          <table class="list">
            <tr>
              <td><strong>Search terms mode</strong><br> 
                <label><input class="termsOr" type="radio" value="true" name="termsOr"
                <?php if(!isset($_GET["termsOr"])||!strcasecmp($_GET["termsOr"], "false") == 0) {?>
                checked <?php } ?>> Or                              
                <?php echo((isset($facetQueryCounts["or"]) && ($facetQueryCounts["or"] > 0) ? "&nbsp;&nbsp;<strong>(".htmlEscape($facetQueryCounts["or"]).")</strong>" : "") . "</label>");?>
                &nbsp;
                <label><input class="termsOr" type="radio" value="false" name="termsOr"
                <?php if(isset($_GET["termsOr"])&&strcasecmp($_GET["termsOr"], "false") == 0) {?>
                checked <?php } ?>> And                
                <?php echo((isset($facetQueryCounts["and"])&& ($facetQueryCounts["and"] > 0) ? "&nbsp;&nbsp;<strong>(".htmlEscape($facetQueryCounts["and"]).")</strong>" : "") . "</label>");?>
                
                <br>&nbsp;<br>
                <strong>Search terms matching</strong><br>
                <label><input class="matchTermsExact" type="radio" value="false" name="matchTermsExact"
                <?php if(isset($_GET["matchTermsExact"])&&strcasecmp($_GET["matchTermsExact"], "false") == 0) {?>
                checked <?php } ?>> Part of word                
                <?php echo((isset($facetQueryCounts["matchPartOfWord"]) && ($facetQueryCounts["matchPartOfWord"]) > 0 ? "&nbsp;&nbsp;<strong>(".htmlEscape($facetQueryCounts["matchPartOfWord"]).")</strong>" : "") . "</label>");?>
                &nbsp;
                <label><input class="matchTermsExact" type="radio" value="true" name="matchTermsExact"
                <?php if(!isset($_GET["matchTermsExact"])||!strcasecmp($_GET["matchTermsExact"], "false") == 0) {?>
                checked <?php } ?>> Whole word
                <?php echo((isset($facetQueryCounts["matchExactWord"]) && ($facetQueryCounts["matchExactWord"]) > 0 ? "&nbsp;&nbsp;<strong>(".htmlEscape($facetQueryCounts["matchExactWord"]).")</strong>" : "") . "</label>");?>
                <br>&nbsp;<br>
                <strong>Search field filters</strong><br>            
            <?php 
            echo("<div class=\"facetbox\">\n");
            echo("<p><strong>Search exclusively in these fields</strong></p>");
            echo("<p>\n");
            for($i=0;$i<count($config["searchfields"]);$i++) {          
              echo("<label><input type=\"checkbox\" class=\"checkboxField\" name=\"field".$i."\" value=\"".htmlEscape(implode(',',$config["searchfields"][$i]["field"]))."\" ".($config["searchfields"][$i]["checked"]?"checked":"")." />\n");
              echo(htmlEscape($config["searchfields"][$i]["description"])."</label><br />\n");
            }
            echo("</p>\n");
            echo("</div>\n");
            echo("<br>\n");
            echo("<a href=\"#\" class=\"button clearFields\">clear all search field filters</a><br>");
          ?>
          
          </td>
            </tr>
            <tr>
              <td>&nbsp;<br>
                <strong>Facet filters</strong>
                <br>
            <?php 
            for($i=0;$i<count($config["facetfields"]);$i++) {
              echo("<table><tbody><tr>");
              //checkbox
              if($config["facetfields"][$i]["type"]=="checkbox") {
                echo("<td><div class=\"facetbox\">\n");
                if($config["facetfields"][$i]["title"]) {
                  echo("<p><strong>".htmlEscape($config["facetfields"][$i]["title"])."</strong></p>");
                }
                echo("<table class=\"facetItem\"><tbody>");

                for($j=0;$j<count($config["facetfields"][$i]["values"]); $j++) {
                  echo("<tr><td class=\"item\"><label><input type=\"checkbox\" class=\"checkboxFacet\" name=\"facet".$i."_".$j."\" value=\"".htmlEscape($config["facetfields"][$i]["values"][$j]["value"])."\" ".($config["facetfields"][$i]["values"][$j]["checked"]?"checked":"")." />\n");
                  echo(htmlEscape($config["facetfields"][$i]["values"][$j]["description"])."</label></td>");
                  echo("<td class=\"count\">&nbsp;&nbsp;(<b>" . getFacetCount($config,$i,$j,$facetFieldCounts) . "</b>)</td></tr>\n");
                }

                echo("</tbody></table>\n");
                
                echo("</td></div>\n");
              //radio
              } else if($config["facetfields"][$i]["type"]=="radio") {
                  echo("<td><div class=\"facetbox\">\n");
                if($config["facetfields"][$i]["title"]) {
                  echo("<p><strong>".htmlEscape($config["facetfields"][$i]["title"])."</strong></p>");
                }
                echo("<table class=\"facetItem\"><tbody><tr>");
                $totalCount = 0;
                for($j=0;$j<count($config["facetfields"][$i]["values"]); $j++) {
                  if ($config["facetfields"][$i]["values"][$j]["value"] != "ALL") {
                    $facetCountDisplayString = getFacetCount($config,$i,$j,$facetFieldCounts) > 0 ? "(<b>" . getFacetCount($config,$i,$j,$facetFieldCounts) . "</b>)" : "";
                    echo("<tr><td class=\"item\"><label><input type=\"radio\" class=\"radioFacet\" name=\"facet".$i."\" value=\"".htmlEscape($config["facetfields"][$i]["values"][$j]["value"])."\" ".($config["facetfields"][$i]["values"][$j]["checked"]?"checked":"")." />\n");
                    echo(htmlEscape($config["facetfields"][$i]["values"][$j]["description"]) . "</label></td><td class=\"count\">&nbsp&nbsp" . $facetCountDisplayString . "</span></td>\n");
                    $totalCount += getFacetCount($config,$i,$j,$facetFieldCounts);
                  }
                  else {
                    echo("<tr><td class=\"item\"><label><input type=\"radio\" class=\"radioFacet\" name=\"facet".$i."\" value=\"".htmlEscape($config["facetfields"][$i]["values"][$j]["value"])."\" ".($config["facetfields"][$i]["values"][$j]["checked"]?"checked":"")." />\n");
                    // dont show totalcounts anymore, because the count can be incorrect if there is more types than the displayed ones..
                    echo("<b>".htmlEscape($config["facetfields"][$i]["values"][$j]["description"])."</b></label></td><td style=\"display:none\" class=\"count\">&nbsp&nbsp(<b>" . $totalCount . "</b>)</span></td>\n");
                  }
                }
                echo("</tr></tbody></table>\n");
                echo("</td></div>\n");
              }
              echo("</tr></tbody></table>");
            } 
            ?>      
            <!-- 
            <input type="radio" class="checkboxFacet" name="facet1" value="1" <?php if(isset($_GET["facet1"])&&($_GET["facet1"]==1)) {?> checked<?php } ?> >Class must be ConceptScheme<br>
             <input type="radio" class="checkboxFacet" name="facet1" value="2" <?php if(isset($_GET["facet1"])&&($_GET["facet1"]==2)) {?> checked<?php } ?> >Class must be Concept<br>
            <input type="checkbox" class="checkboxFacet" name="facet2" value="1" <?php if(isset($_GET["facet2"])&&$_GET["facet2"]) {?> checked<?php } ?> >Created By User 0<br>
            //-->
                &nbsp;<br>
                <a href="#" class="button clearFacets">clear all facets</a>
                
              </td>
            </tr>
          </table>
        </td>
        <td id="results">
      
      
      


<?php
if(isset($_GET["id"]) && $_GET["id"] && $data = getItem(get_magic_quotes_gpc()?stripslashes($_GET["id"]):$_GET["id"], $solrUrl)) {
  
  $key = (isset($_GET["key"])&&$_GET["key"])?$_GET["key"]:"";
  $matchTermsExact = isset($_GET["matchTermsExact"])?$_GET["matchTermsExact"]:false;
  $termsOr = isset($_GET["termsOr"])?$_GET["termsOr"]:false;

  ?>
  <p>
            <a class="button" href="j#" onclick="window.history.back();">back</a>
            <!-- 
            <a class="button"
              href="<?php echo($_SERVER["PHP_SELF"]."?start=".urldecode($start)."&number=".urldecode($number)."&matchTermsExact=".$matchTermsExact."&termsOr=".$termsOr."&key=".$key.$urlSearchField.$urlFacetField) ?>">back</a>
            //-->  
          </p>
          <table class="resultList"><tbody>
            <tr class="title">
              <td>Field</td>
              <td>Value</td>
            </tr>
    <?php
    $tmp_i=0;
    $filteredData = filteritem($data);
    $topConceptsConceptScheme = getTopConceptsConceptScheme($data["uri"], $solrUrl);
    $topConceptsSkosCollection = getTopConceptsSkosCollection($data["uri"], $solrUrl);
    foreach($filteredData AS $tmp_key => $tmp_value) {
      echo("<tr".(!($tmp_i%2)?" class=\"odd\"":"")."><td class=\"fieldname\">".htmlEscape($tmp_key)."</td><td class=\"value\">".printValue($tmp_key,$tmp_value)."</td></tr>\n");
      $tmp_i++;
    }
    ?>
  </tbody></table>
          <p>
            <a class="button" href="j#" onclick="window.history.back();">back</a>
            <!--  
            <a class="button"
              href="<?php echo($_SERVER["PHP_SELF"]."?start=".urldecode($start)."&number=".urldecode($number)."&matchTermsExact=".$matchTermsExact."&termsOr=".$termsOr."&key=".$key.$urlSearchField.$urlFacetField) ?>">back</a>
            //--> 
          </p>
  <?php
} 
else {

  if(is_array($list) && (count($list)>0)) {    
    ?><strong>Concepts found: </strong><?php
      echo(($start+1)." to ".($start+count($list))." of ".$total." concepts\n");
      echo("<p>");
      if($start>0) {
        echo("<a class=\"button\" href=\"".$_SERVER["PHP_SELF"]."?start=".max(0,($start-$number))."&number=".$number."&matchTermsExact=".$matchTermsExact."&termsOr=".$termsOr."&key=".$key.$urlSearchField.$urlFacetField."\">previous ".min($start,$number)."</a>&nbsp;");
      } else {
        //echo("previous 0&nbsp;");
      }
      if(($start+$number)<$total) {
        echo("<a class=\"button\" href=\"".$_SERVER["PHP_SELF"]."?start=".($start+$number)."&number=".$number."&matchTermsExact=".$matchTermsExact."&termsOr=".$termsOr."&key=".$key.$urlSearchField.$urlFacetField."\">next ".min($number,($total-$start-$number))."</a>&nbsp;");
      } else {
        //echo("next 0&nbsp;");
      }
      echo("</p>");
    ?> 
    <table class="resultList">
            <tbody><tr class="title">
              <td width="30%">URI</td>
              <td width="30%">Label</td>
              <td width="40%">Definition</td>
            </tr>
      <?php      
      for($tmp_i=0;$tmp_i<count($list);$tmp_i++) {
        $item = $list[$tmp_i];
        echo("<tr".((!($tmp_i%2))?" class=\"odd\"":"").">\n");
        echo("  <td><a href=\"".$_SERVER["PHP_SELF"]."?id=" .urlencode($item["uri"]) . "&start=".$start."&number=".$number."&matchTermsExact=".$matchTermsExact."&termsOr=".$termsOr."&key=".$key.$urlSearchField.$urlFacetField."\">" .htmlEscape($item["uri"])."</a>&nbsp;");
        echo("  <td>".printValue("prefLabel@en",isset($item["prefLabel@en"])?$item["prefLabel@en"][0]:"")."</td>\n");
        echo("  <td>".printValue("definition@en",isset($item["definition@en"])?$item["definition@en"][0]:"")."</td>\n");
        echo("<tr>\n");
      }
      
      
      
      ?>
    </tbody></table>
    <!--  
    <table class="resultList">
      <tr class="title"><td>&nbsp;</td><td>Titel</td><td>Omschrijving</td><td>Lala</td></tr>
      <?php
      foreach($list AS $item) {
        
        echo("<tr>\n");
  
        foreach($item as $key => $value) {
        echo("  <td>".printValue($key, $value)."</td>\n");;
    }
        
        echo("<tr>\n");
        
      }
      ?>
    </table>
    -->
            
    <?php
  }
  else {
    echo("No concepts were found for these search criteria.");
  }
  
  
  
}

?>
        </td>
            </tr>





            </form>

            
<!-- Eind wrap --->
</div>
</body></html>            
<?php

/* FUNCTIES */

function getFacetCount($config, $i, $j, $facetFieldCounts) {
  // Find the facet count..
  $facetCountNumber = "0"; // when count is not in response its 0..
  // loop over the returned facet counts...
  foreach($facetFieldCounts AS $key => $value) {
    if ($key === $config["facetfields"][$i]["facet"]) { // general type (i.e. 'class') is in result...
      for ($p=0; $p<count($value); $p+=2) {
        if ($value[$p] == $config["facetfields"][$i]["values"][$j]["value"]) { // type (i.e. 'Concept') is in result...
          // get the next value, the count in in solrs next facet count response value...
          return $value[$p+1];
        }
      }
    }
  }
  return $facetCountNumber;
}

function getItem($id, $solr_baseurl) {
  $query = "q=uri:".solrEscape($id);
  if($response = solrRequest($query, $solr_baseurl)) {
    if(
      isset($response["response"]) && 
      isset($response["response"]["docs"]) && 
      count($response["response"]["docs"])>0) {
      return $response["response"]["docs"][0];
    }
  }
  return false;
}

function filterItem($data) {
	$superBelangrijkeVelden = array("class","status","prefLabel@","definition@",
			                                      "notation@","example@","note@","scopeNote@","changeNote@",
			                                      "inScheme","inSkosCollection","hasTopConcept","deleted","toBeChecked","uri");
	$onzinnigeKutPatronenDieJeKanNegeren = array("DocumentationProperties*","LexicalLabels*","prefLabelAutocomplete*");
	$onzinnigeKutVeldenDieJeKanNegeren = array("collection","tenant","approved_by","created_by",
			                                       "deleted_by","modified_by","uuid","xml","xmlns",                                       
			                                       "timestamp","modified_timestamp");
	if(is_array($data)) {
		$newdata1 = array();
		$newdata2 = array();
		//important first
		foreach($superBelangrijkeVelden AS $item) {
			if(preg_match("/^(.*?)\@$/",$item,$match)) {
			  $sublist = array();				
				foreach($data AS $key => $value) {
					if(substr($key,0,strlen($match[0]))==$match[0]) {
						$sublist[$key] = $value;
					}
				}
				if(count($sublist)==0) {
					if(isset($data[$match[1]])) {
						$sublist[$match[1]] = $data[$match[1]];						
					}
				} else {
					if(!in_array($match[1],$onzinnigeKutVeldenDieJeKanNegeren)) {
						$onzinnigeKutVeldenDieJeKanNegeren[] = $match[1];
					}
				}
				ksort($sublist);
				$newdata1 = array_merge($newdata1, $sublist);
			} else if(isset($data[$item])) {
				$newdata1[$item] = $data[$item];
			}
		}
		//filter shit
		foreach($onzinnigeKutPatronenDieJeKanNegeren AS $item) {
			if(preg_match("/^(.*?)\*$/",$item,$match)) {
				foreach($data AS $key => $value) {
					if(substr($key,0,strlen($match[1]))==$match[1]) {
						if(!in_array($key, $onzinnigeKutVeldenDieJeKanNegeren)) {
						  $onzinnigeKutVeldenDieJeKanNegeren[] = $key;
						}  
					}
				}				
			}	
		}
		//show others
		foreach($data AS $key => $value) {
			if(!in_array($key, $onzinnigeKutVeldenDieJeKanNegeren) && !isset($newdata1[$key])) {
				$newdata2[$key] = $value;
			}
		}
		//sort others
		ksort($newdata2);
		//merge shit
		return array_merge($newdata1, $newdata2);		
	} else {
		return false;
	}
}

// Calls solr with search parameters and returns the result
function getList($start=0, $num=10, $config, $solrUrl) {  
  // build facet filter query part...
  $activeFacetFiltersMap = array();
  $activeFacetsFilterQueryString = "";
  foreach ($config["facetfields"] AS $item) {
    if($item["type"]=="radio") {
      foreach($item["values"] AS $subitem) {
        if($subitem["value"] != "ALL" && $subitem["checked"]) {
          $activeFacetFiltersMap[$item["facet"]] = $subitem["value"];
        }
      }
    } else if($item["type"]=="checkbox") {
      foreach($item["values"] AS $subitem) {
        if($subitem["checked"]) {
        	if(!isset($activeFacetFiltersMap[$item["facet"]])) {
        		$activeFacetFiltersMap[$item["facet"]] = array();
        	}
          $activeFacetFiltersMap[$item["facet"]][] = $subitem["value"];
        }
      }
    }
  }
  $activeFacetsFilterQueryString = buildSolrActiveFacetFiltersString($activeFacetFiltersMap);

  // build facet query part..
  $allFacetFieldsQueryString = "&facet=true";
  foreach ($config["facetfields"] AS $item) {
    $allFacetFieldsQueryString  .= "&facet.field={!ex=" .$item["facet"] . "}" . $item["facet"];
  }

  // collect terms
  $searchTermsEntered = array();
  if (!isset($_GET["key"]) Or trim($_GET["key"]) == "") {
    //do nothing
  } else {
  	$tmp_key = trim($_GET["key"]);
    if(!preg_match("/\"/",$tmp_key)) {
    	//$searchTermsEntered = explode(" ", trim($tmp_key));
    	$tmp_searchTermsEntered = explode(" ", trim($tmp_key));
    	foreach($tmp_searchTermsEntered AS $tmp_searchTermsEntered_item) {
    		if(trim($tmp_searchTermsEntered_item)) {
    			$searchTermsEntered[] = array("value"=>trim($tmp_searchTermsEntered_item), "phrase" =>false);
    		}
    	}    	
    } else {
    	$searchTermsEntered = array();
    	$tmp_list = explode("\"", $tmp_key); 
    	for($tmp_i=0;$tmp_i<count($tmp_list); $tmp_i++) { 
    		if($tmp_i%2) {
    			if(trim($tmp_list[$tmp_i])) {
    				$searchTermsEntered[] = array("value"=>trim($tmp_list[$tmp_i]), "phrase"=>true); 
    			}
    		}	else {
    			$tmp_searchTermsEntered = explode(" ", trim($tmp_list[$tmp_i]));
    			foreach($tmp_searchTermsEntered AS $tmp_searchTermsEntered_item) {
    				if(trim($tmp_searchTermsEntered_item)) {
    					$searchTermsEntered[] = array("value"=>trim($tmp_searchTermsEntered_item), "phrase"=>false);
    				}
    			}
    		}
    	}
    }
  }  
  //get settings search
  $exactTermMatching = (isset($_GET["matchTermsExact"]) && strcasecmp($_GET["matchTermsExact"], "false") == 0) ? false : true; // asterisk for searching part of terms..
  $orTerms = isset($_GET["termsOr"]) && strcasecmp($_GET["termsOr"], "false") == 0 ? false : true; // OR is default  
  //get values
  $searchValues = array();
  if (count($searchTermsEntered) > 0) {
  	foreach ($searchTermsEntered as $term ) {
  		$searchValues[] = $term;
  	}
  } else {
  	$searchValues[] = array("value"=>"*", "phrase"=>false);
  }    
  //get fields
  $searchFields = array();
  foreach ($config["searchfields"] AS $item) {
    if($item["checked"]) {
      $searchFields[] = $item["field"];
    }
  }
  if (empty($searchFields)) { // no search field filters activated
  	foreach ($config["searchfields"] AS $item) {
  		$searchFields[] = $item["field"];
  	} 
  } 	 
  
  // assemble total query and use search terms...
  // build all search field filters query string..
  $allSearchFieldsQueryString = "";
  $queryFiltersTagName = "queryFiltersTag";
  
  $queryLucenePrefix = "NOT_INITIALIZED";
  $rangeString = "&start=".intval($start) . "&rows=" . intval ( $num );
  $queryPostfix =  $activeFacetsFilterQueryString . $rangeString . $allFacetFieldsQueryString;
  
  $filterQueryLucenePrefix = buildLuceneFilterQueryString($searchFields, $searchValues, $orTerms, $exactTermMatching);
  $queryLucenePrefix = buildLuceneFilterQueryString($searchFields, $searchValues, "OR", false);
  $query = "fq={!tag=" . $queryFiltersTagName ."}" . $filterQueryLucenePrefix . $queryPostfix;
  $query.= "&q=((" . $queryLucenePrefix . ") OR (*:*)) AND deleted:false";

  /* Facet Queries :  */  
  $activeFacetFilterMapString = "";
  foreach($activeFacetFiltersMap as $key => $value) {
    if(is_string($value)) {
        $activeFacetFilterMapString[] = $key . ":" . $value;
    } else if(is_array($value)) {
        foreach($value AS $subvalue) {
            $activeFacetFilterMapString[] = $key . ":" . $subvalue;
    	}
    }
  }
  
  $facetQueries = array();
  $totalActiveFacetFilterMapString = empty($activeFacetFilterMapString) ? "" : " AND ". implode(" AND ", $activeFacetFilterMapString);
  //and/or search terms counts
  if (count($searchTermsEntered) > 0) { // if one or more term entered.. (not > 1 , because default search terms can already be more than one term..)
    $facetQueries[] = "{!ex=" . $queryFiltersTagName ." key=or}(" . buildLuceneFilterQueryString($searchFields, $searchValues, true, $exactTermMatching) . ")" . $totalActiveFacetFilterMapString;
    $facetQueries[] = "{!ex=" . $queryFiltersTagName ." key=and}(". buildLuceneFilterQueryString($searchFields, $searchValues, false, $exactTermMatching) . ")" . $totalActiveFacetFilterMapString;
  }  
  
  // match word exact/non exact
  if (count($searchTermsEntered) > 0) { // if one or more term entered..
    $facetQueries[] = "{!ex=" . $queryFiltersTagName ." key=matchExactWord}(" . buildLuceneFilterQueryString($searchFields, $searchValues, $orTerms, true) . ")" . $totalActiveFacetFilterMapString;
    $facetQueries[] = "{!ex=" . $queryFiltersTagName ." key=matchPartOfWord}(". buildLuceneFilterQueryString($searchFields, $searchValues, $orTerms, false) . ")" . $totalActiveFacetFilterMapString;
  }
  
  foreach($facetQueries AS $value) {
    $query .= "&facet.query=" . urlencode($value);
  }

  // debugging statement, handy to use for debugging generated queries
  //var_dump(urldecode("Query String ======: q=*:*&". $query));

  // Call SOLR
  if($response = solrRequest("sort=score desc&".$query, $solrUrl)) {
    if(isset($response["response"]) && isset($response["response"]["numFound"]) && isset($response["response"]["docs"]) && isset($response["facet_counts"]) && isset($response["facet_counts"]["facet_fields"])) {
      return array($response["response"]["docs"], intval($response["response"]["numFound"]), $response["facet_counts"]["facet_fields"], $response["facet_counts"]["facet_queries"]);
    }
  }


  return false;
}

function buildLuceneFilterQueryString($searchFields, $searchValues, $isOrSearch, $exactWordMatching) {
  $queryList = array();  
  foreach($searchValues AS $value) {
  	$subqueryList = array();
  	foreach($searchFields AS $fields) {
  		foreach($fields AS $field) {
  			if($value["phrase"]) {
  			  $subqueryList[] =  "(" . $field . ":\"" .solrEscape($value["value"], false) . "\")"; 
  			} else if($exactWordMatching OR (!$exactWordMatching AND ($value["value"]=="*"))) {  			
  			  $subqueryList[] =  "(" . $field . ":" . solrEscape($value["value"], true) . ")"; 
  			} else {
  			  $subqueryList[] =  "(" . $field . ":*" .solrEscape($value["value"], true) . "*)"; 
  			}    			
  		}
  	}
  	$queryList[] = "(".implode(") OR (", $subqueryList).")";
  }
  
  if($isOrSearch) {
  	return "(".implode(") OR (", $queryList).")";
  } else {
  	return "(".implode(") AND (", $queryList).")";
  }    
}

function buildSolrActiveFacetFiltersString($activeFacetFiltersMap) {
  $activeFacetsFilterQueryString = "";
  foreach($activeFacetFiltersMap as $key => $value) {
    if(is_string($value)) {
    	$activeFacetsFilterQueryString .= "&fq={!tag=". $key . "}" . urlencode($key . ":" . solrEscape($value));
    } else if(is_array($value)) {
    	$list = array();
    	foreach($value AS $subvalue) {
    		$list[] = "(".$key.":".solrEscape($subvalue).")";
    	}
    	if(count($list)>0) {
    		$activeFacetsFilterQueryString .= "&fq={!tag=". $key . "}" . urlencode(implode(" OR ", $list));
    	}
    }
  }
  return $activeFacetsFilterQueryString;
}

function printValue($field,$value) {
	GLOBAL $conceptSchemes;
	GLOBAL $skosCollections;
	GLOBAL $topConceptsConceptScheme;
	GLOBAL $topConceptsSkosCollection;
	if(is_string($value)) {
    //if($value && in_array($field, array("uri"))) { // uitbreiden als er meer verwijs velden bij komen..
    //  return "<a href=\"../api/concept?id=".$value."\">".htmlEscape("".$value."")."</a>";
    //} 
    $translated_value = $value;
    if($value && ($field=="inScheme")) { 
    	foreach($conceptSchemes AS $item) {
    		if($value==$item["value"]) {
    			$translated_value = $item["description"];
    		}
    	} 
    } else if($value && ($field=="inSkosCollection")) { 
    	foreach($skosCollections AS $item) {
    		if($value==$item["value"]) {
    			$translated_value = $item["description"];    			
    		}
    	}    	
    } else if($value && ($field=="hasTopConcept")) { 
    	foreach($topConceptsConceptScheme AS $item) {
    		if($value==$item["uri"]) {
    			if(isset($item["prefLabel"]) && (count($item["prefLabel"])>0)) {
    			  $translated_value = $item["prefLabel"][0];
    			}     			
    		}
    	}
    	foreach($topConceptsSkosCollection AS $item) {
    		if($value==$item["uri"]) {
    			if(isset($item["prefLabel"]) && (count($item["prefLabel"])>0)) {
    				$translated_value = $item["prefLabel"][0];
    			}    			
    		}
    	}
    }     
    if($value && in_array($field, array("uri","inScheme","inSkosCollection","hasTopConcept","topConceptOf"))) { // uitbreiden als er meer verwijs velden bij komen..
      return "<a href=\"".$_SERVER["PHP_SELF"]."?id=".urlencode($value)."\">".htmlEscape($translated_value)."</a>";
    } else {
      return(htmlEscape($value));
    }
  } else if(is_int($value)) {
    return(htmlEscape($value)); 
  } else if(is_bool($value)) {
    return $value?"Yes":"---";
  } else if(is_array($value)) {
    if(count($value)<1) {
  		return printValue($field, "");
  	} else if(count($value)==1) {
  		return printValue($field, $value[0]);
  	} else {
  		$text = "<table>\n";
	    foreach($value AS $subkey => $subvalue) {
	      $text.="<tr><td class=\"item\">".printValue($field,$subvalue)."</td></tr>\n";
	    } 
	    $text.= "</table>\n";
	    return $text;
  	}  
  } else {
    return "<i>ONBEKEND TYPE (veld ".htmlEscape($field).")</i>";
  }
}

function compareListItems($a, $b) {
	return strcmp($a["description"], $b["description"]);
}

function getConceptSchemes($solr_baseurl) {
	$query = "q=class:ConceptScheme deleted:false&fl=dcterms_title,uri,uuid&rows=100000";
	if($response = solrRequest($query, $solr_baseurl)) {
		if(is_array($response) && isset($response['response']) && is_array($response['response']) && isset($response['response']['docs']) && is_array($response['response']['docs'])) {
			$list = array();
			foreach($response['response']['docs'] AS $item) {
				if(isset($item['uri']) && is_string($item['uri']) && isset($item['dcterms_title']) && is_array($item['dcterms_title'])) {
					$list[] = array("value" => $item['uri'], "description" => $item['dcterms_title'][0]);
				}
			}
			usort($list, "compareListItems");
			return $list;
		}
	} else {
		return array();
	}
}

function getSkosCollections($solr_baseurl) {
  $query = "q=class:SKOSCollection AND deleted:false&fl=dcterms_title,uri,uuid&rows=100000";
  if($response = solrRequest($query, $solr_baseurl)) {
    if(is_array($response) && isset($response['response']) && is_array($response['response']) && isset($response['response']['docs']) && is_array($response['response']['docs'])) {
      $list = array();
      foreach($response['response']['docs'] AS $item) {
        if(isset($item['uri']) && is_string($item['uri']) && isset($item['dcterms_title']) && is_array($item['dcterms_title'])) {
          $list[] = array("value" => $item['uri'], "description" => $item['dcterms_title'][0]);
        }  
      }
      usort($list, "compareListItems");
      return $list;
    }  
  } else {
    return array();
  }  
}

function getTopConceptsConceptScheme($schemeUri, $solr_baseurl) {
	$query = "q=inScheme:".urlencode(solrEscape($schemeUri))." AND deleted:false&fl=uri,prefLabel&rows=100000";
  if($response = solrRequest($query, $solr_baseurl)) {
		if(is_array($response) && isset($response['response']) && is_array($response['response']) && isset($response['response']['docs']) && is_array($response['response']['docs'])) {
			return $response['response']['docs'];
		}
	} 
	return array();
}

function getTopConceptsSkosCollection($schemeUri, $solr_baseurl) {
	$query = "q=inSkosCollection:".urlencode(solrEscape($schemeUri))." AND deleted:false&fl=uri,prefLabel&rows=100000";
	if($response = solrRequest($query, $solr_baseurl)) {
		if(is_array($response) && isset($response['response']) && is_array($response['response']) && isset($response['response']['docs']) && is_array($response['response']['docs'])) {
			return $response['response']['docs'];
		}
	}
	return array();
}

function solrRequest($query, $solr_baseurl) {
  //dirty logger
  GLOBAL $queryLogger;
  $queryLogger[] = $query;
  //do magic
	$query.="&wt=json";  
  $ch = curl_init($solr_baseurl."select/");
  $options = array(
        CURLOPT_HTTPHEADER => array ("Content-Type:application/x-www-form-urlencoded; charset=utf-8"),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $query
  );
  curl_setopt_array( $ch, $options );
  $result =  curl_exec($ch);
  if($data = json_decode($result, true)) {
    return $data;
  } else {
    echo "<script type='text/javascript'>alert('Sorry, the search server is not reachable at this moment. Please try again at a later time.');</script>";
    return false;
  }
}

function solrEscape($text, $allowWildcard=false) {
  if($allowWildcard) {
  	$match = array('\\', '+', '-', '&', '|', '!', '(', ')', '{', '}', '[', ']', '^', '~', ':', '"', ';', ' ');  
    $replace = array('\\\\', '\\+', '\\-', '\\&', '\\|', '\\!', '\\(', '\\)', '\\{', '\\}', '\\[', '\\]', '\\^', '\\~', '\\:', '\\"', '\\;', '\\ ');
  } else {
  	$match = array('\\', '+', '-', '&', '|', '!', '(', ')', '{', '}', '[', ']', '^', '~', '*', '?', ':', '"', ';', ' ');
  	$replace = array('\\\\', '\\+', '\\-', '\\&', '\\|', '\\!', '\\(', '\\)', '\\{', '\\}', '\\[', '\\]', '\\^', '\\~', '\\*', '\\?', '\\:', '\\"', '\\;', '\\ ');  	
  }  
  $text = str_replace($match, $replace, $text);
  if(!preg_match("/ /",$text)) {
    $string = "\"".$text."\"";
  }
  return $text;
}

function htmlEscape($text) {
  return htmlentities($text,ENT_COMPAT,"UTF-8");
}


?>
