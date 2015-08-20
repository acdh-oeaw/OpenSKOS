<?php 

header("Location: index.php");
exit();

/// IMPORTANT: The ALL (Any) option for a radio button facet should always be declared last in the config
// Dynamic facets are facets which values can be set at runtime (depending on serach server search results)
$config = array(
		"searchfields" => array(
			array("field" => array("definition"), "description"=>"Definition"),
			array("field" => array("DocumentationProperties"), "description"=>"Documentation properties"),
			array("field" => array("DocumentationPropertiesText", "LexicalLabelsPhrase"), "description"=>"Default Concept Registry Textual Search  Fields") // same as in the Concept Registry OpenSkos search application
		),
  		"facetfields"=> array(
		array("title" => "Object type", "type" => "radio", "facet"=>"class", "values" => array(
				array("value"=>"SKOSCollection", "description" => "Skos Collection"),
				array("value"=>"ConceptScheme", "description" => "Concept Scheme"),
				array("value"=>"Concept", "description" => "Concept"),
				array("value"=>"ALL", "description" => "Any")
		     )
        ),
  		array("title" => "Collection", "type" => "radio", "facet"=>"collection", "dynamic"=>true, "values" => array( //  dynamic facet values based on "facet" name
  				array("value"=>"ALL", "description" => "Any")
  			)
  		),
  		array("title" => "Tenants", "type" => "radio", "facet"=>"tenant", "dynamic"=>true, "values" => array( //  dynamic facet values based on "facet" name
  				array("value"=>"ALL", "description" => "Any")
  		)
  		),
  		array("title" => "Approved by", "type" => "radio", "facet"=>"approved_by", "dynamic"=>true, "values" => array( //  dynamic facet values based on "facet" name
  				array("value"=>"ALL", "description" => "Any")
  		)
  		),
		array("title" => "User", "type" => "checkbox", "facet"=>"created_by", "values" => array(
				array("value"=>"0", "description" => "Created by user 0"),
		        array("value"=>"1", "description" => "Created by user 1")
             )
        ),
  		array("title" => "Status", "type" => "radio", "facet"=>"status", "values" => array(
  				array("value"=>"approved", "description" => "Approved"),
  				array("value"=>"candidate", "description" => "Candidate"),
  				array("value"=>"ALL", "description" => "Any")
  			 )
  		)
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


?><html>
<head>
<title>Concept Registry Browser</title>
<style>
body {
	background-color: #EEEEEE;
}

h1 {
	color: #FF0000;
}

input.text {
  border: 1px solid #000000;
  font-size: 120%;
}

input.button {
  border: 1px solid #000000;
  font-size: 120%;
}

table.list {
	border-collapse: collapse;
}

table.list td {
	vertical-align: top;
	border: 1px solid #555555;
}

table.list td.item {
	border-style: none;
}

table.list tr.title td {
	font-weight: bold;
	font-size: 120%;
}

table.list td.fieldname {
	width: 250px;
	color: #555555;
}

td.count {
	text-align: right;
}

div.facetbox, div.searchbox, div.clearFacetsTable {
	border-style: solid;
	border-width: 1px;
	border-color: #0000FF;
	background: #FFFFFF;
	margin: 0 3px 5px 3px;
	padding: 2px;
}

div.facetbox span.item, div.searchbox span.item {
	white-space: nowrap;
}

a.clearFields {
	font-weight: bold;
}

a.clearFacets {
	font-weight: bold;
}

table.facetItem td {
	border-style: none;
}
</style>
<script src="../scripts/jquery-1.11.1.min.js"></script>


<script type="text/javascript">

//     $(document).ready(

//     	    function() {

//     	       $.ajax({
//     	          url: "getMYSQLServerData.php?function=getServerData",
//     	          type: "GET",
//     	          async: false,
//     	          success: function(data){
//     	              //Do something here with the "data"
//     	        	  //$(document.body).prepend("<b>" . data "</b>");
//     	        	  //document.write("<b>" + data + "</b>");
//     	        	  alert("<b>" + data + "</b>");
//     	          }
//     	       });

//     });
    
  	$(document).ready(function() {  	  	

        $("a.clearFields").click(function() 
	        { 
		      $("form#formulier input.checkboxField").prop("checked", false);
		      $("form#formulier").submit();
		      return false;
		    });

	    $("form#formulier input.checkboxField").click(function(){ $("form#formulier").submit(); });

	    $("a.clearFacets").click(function() 
            { 
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
</head>



<body>
  
  <?php 
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
  $users = $getData->getUsers($dbHost, $dbUserName, $dbPassword, $dbDbname);
  $tenants = $getData->getTenants($dbHost, $dbUserName, $dbPassword, $dbDbname);
	
  
  $start = isset($_GET["start"])?intval($_GET["start"]):0;
  $number = isset($_GET["number"])?max(1,intval($_GET["number"])):10;
  
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
  			else if ($config["facetfields"][$i]["facet"] == "approved_by") {
  				foreach($users as $userID => $userName) {
  					$config["facetfields"][$i]["values"][] = array("value" => $userID, "description" => $userName);
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
				
  				
  				
  
  ?>
  	<form id="formulier" action="" method="GET">
		<h1>Concept Registry Browser</h1>
		<p>
			<strong>Please type one or more space separated search terms</strong>
		</p>
		</strong>
		<input class="text" type="text" size="25" name="key"
			value="<?php if(isset($_GET["key"])&&$_GET["key"]) { echo(htmlentities($_GET["key"]));} ?>">
		<input class="button" type="submit" value="Search">
    <input class="button" type="button" onclick="location.href='<?php echo(htmlentities($_SERVER["PHP_SELF"]));?>';" value="Reset all"><br />
		<br />
		<table class="list">
			<tr>
				<td>
					<table class="list">
						<tr>
							<td><label>Search terms mode:</label><br> 
								<input class="termsOr" type="radio" value="true" name="termsOr"
								<?php if(!isset($_GET["termsOr"])||!strcasecmp($_GET["termsOr"], "false") == 0) {?>
								checked <?php } ?>> Or								
								
								<?php echo((isset($facetQueryCounts["or"]) && ($facetQueryCounts["or"] > 0) ? "&nbsp;&nbsp;<strong>(".htmlentities($facetQueryCounts["or"]).")</strong>" : "") . "</br>");?>
								
								<input class="termsOr" type="radio" value="false" name="termsOr"
								<?php if(isset($_GET["termsOr"])&&strcasecmp($_GET["termsOr"], "false") == 0) {?>
								checked <?php } ?>> And
								
								<?php echo((isset($facetQueryCounts["and"])&& ($facetQueryCounts["and"] > 0) ? "&nbsp;&nbsp;<strong>(".htmlentities($facetQueryCounts["and"]).")</strong>" : "") . "</br>");?>
								
								<label>Search terms matching:</label><br>
								<input class="matchTermsExact" type="radio" value="false" name="matchTermsExact"
								<?php if(isset($_GET["matchTermsExact"])&&strcasecmp($_GET["matchTermsExact"], "false") == 0) {?>
								checked <?php } ?>> Part of word
								
								<?php echo((isset($facetQueryCounts["matchPartOfWord"]) && ($facetQueryCounts["matchPartOfWord"]) > 0 ? "&nbsp;&nbsp;<strong>(".htmlentities($facetQueryCounts["matchPartOfWord"]).")</strong>" : "") . "</br>");?>
								
								<input class="matchTermsExact" type="radio" value="true" name="matchTermsExact"
								<?php if(!isset($_GET["matchTermsExact"])||!strcasecmp($_GET["matchTermsExact"], "false") == 0) {?>
								checked <?php } ?>> Whole word
								<?php echo((isset($facetQueryCounts["matchExactWord"]) && ($facetQueryCounts["matchExactWord"]) > 0 ? "&nbsp;&nbsp;<strong>(".htmlentities($facetQueryCounts["matchExactWord"]).")</strong>" : "") . "</br>");?>
								<h4>Search field filters</h4>
								<br>
						<?php 
						echo("<div class=\"searchbox\">\n");
						echo("<p><strong>Search exclusively in these fields :</label></strong><p>");
						for($i=0;$i<count($config["searchfields"]);$i++) {					
						  echo("<span class=\"item\"><input type=\"checkbox\" class=\"checkboxField\" name=\"field".$i."\" value=\"".htmlentities(implode(',',$config["searchfields"][$i]["field"]))."\" ".($config["searchfields"][$i]["checked"]?"checked":"")." />\n");
						  echo(htmlentities($config["searchfields"][$i]["description"])."</span><br />\n");
						}
						echo("&nbsp;<br>");
						echo("<a href=\"#\" class=\"clearFields\">clear all search field filters</a><br>");
                        echo("</div>\n"); 
                        ?>
					
					</td>
						</tr>
						<tr>
							<td>
								<h4>Facet filters</h4>
								<br>
						<?php 
						for($i=0;$i<count($config["facetfields"]);$i++) {
							echo("<table><tr>");
							//checkbox
							if($config["facetfields"][$i]["type"]=="checkbox") {
								echo("<td><div class=\"facetbox\">\n");
								if($config["facetfields"][$i]["title"]) {
									echo("<p><strong>".htmlentities($config["facetfields"][$i]["title"])."</strong></p>");
								}
								echo("<table class=\"facetItem\"><tr>");

								for($j=0;$j<count($config["facetfields"][$i]["values"]); $j++) {
									echo("<tr><td><span class=\"item\"><input type=\"checkbox\" class=\"checkboxFacet\" name=\"facet".$i."_".$j."\" value=\"".htmlentities($config["facetfields"][$i]["values"][$j]["value"])."\" ".($config["facetfields"][$i]["values"][$j]["checked"]?"checked":"")." />\n");
									echo(htmlentities($config["facetfields"][$i]["values"][$j]["description"])."</td>");
									echo("<td class=\"count\">&nbsp;&nbsp;(<b>" . getFacetCount($config,$i,$j,$facetFieldCounts) . "</b>)</td>\n");
								}

								echo("</tr></table>\n");
								
								echo("</td></div>\n");
							//radio
							} else if($config["facetfields"][$i]["type"]=="radio") {
							    echo("<td><div class=\"facetbox\">\n");
								if($config["facetfields"][$i]["title"]) {
									echo("<p><strong>".htmlentities($config["facetfields"][$i]["title"])."</strong></p>");
								}
								echo("<table class=\"facetItem\"><tr>");
								$totalCount = 0;
								for($j=0;$j<count($config["facetfields"][$i]["values"]); $j++) {
									if ($config["facetfields"][$i]["values"][$j]["value"] != "ALL") {
										$facetCountDisplayString = getFacetCount($config,$i,$j,$facetFieldCounts) > 0 ? "(<b>" . getFacetCount($config,$i,$j,$facetFieldCounts) . "</b>)" : "";
										echo("<tr><td><span class=\"item\"><input type=\"radio\" class=\"radioFacet\" name=\"facet".$i."\" value=\"".htmlentities($config["facetfields"][$i]["values"][$j]["value"])."\" ".($config["facetfields"][$i]["values"][$j]["checked"]?"checked":"")." />\n");
										echo(htmlentities($config["facetfields"][$i]["values"][$j]["description"]) . "</td><td class=\"count\">&nbsp&nbsp" . $facetCountDisplayString . "</span></td>\n");
										$totalCount += getFacetCount($config,$i,$j,$facetFieldCounts);
									}
									else {
										echo("<tr><td><span class=\"item\"><input type=\"radio\" class=\"radioFacet\" name=\"facet".$i."\" value=\"".htmlentities($config["facetfields"][$i]["values"][$j]["value"])."\" ".($config["facetfields"][$i]["values"][$j]["checked"]?"checked":"")." />\n");
										// dont show totalcounts anymore, because the count can be incorrect if there is more types than the displayed ones..
										echo("<b>".htmlentities($config["facetfields"][$i]["values"][$j]["description"])."</b>" . "</td><td style=\"display:none\" class=\"count\">&nbsp&nbsp(<b>" . $totalCount . "</b>)</span></td>\n");
									}
								}
								echo("</tr></table>\n");
								echo("</td></div>\n");
							}
							echo("</tr></table>");
						} 
						?>			
						<!-- 
						<input type="radio" class="checkboxFacet" name="facet1" value="1" <?php if(isset($_GET["facet1"])&&($_GET["facet1"]==1)) {?> checked<?php } ?> >Class must be ConceptScheme<br>
					 	<input type="radio" class="checkboxFacet" name="facet1" value="2" <?php if(isset($_GET["facet1"])&&($_GET["facet1"]==2)) {?> checked<?php } ?> >Class must be Concept<br>
						<input type="checkbox" class="checkboxFacet" name="facet2" value="1" <?php if(isset($_GET["facet2"])&&$_GET["facet2"]) {?> checked<?php } ?> >Created By User 0<br>
						//-->
								<div class="clearFacetsTable">
									<table class="list">
										<tr>
											<td><a href="#" class="clearFacets">clear all facets</a>
										
										</tr>
									</table>
								</div>
							</td>
						</tr>
					</table>
				</td>
				<td>
			
			
			


<?php
if(isset($_GET["id"]) && $_GET["id"] && $data = getItem(get_magic_quotes_gpc()?stripslashes($_GET["id"]):$_GET["id"], $solrUrl)) {
	
	$key = (isset($_GET["key"])&&$_GET["key"])?$_GET["key"]:"";
	$matchTermsExact = isset($_GET["matchTermsExact"])?$_GET["matchTermsExact"]:false;
	$termsOr = isset($_GET["termsOr"])?$_GET["termsOr"]:false;

  ?>
  <p>
						<a
							href="<?php echo($_SERVER["PHP_SELF"]."?start=".urldecode($_GET["start"])."&number=".urldecode($_GET["number"])."&matchTermsExact=".$matchTermsExact."&termsOr=".$termsOr."&key=".$key.$urlSearchField.$urlFacetField) ?>">back</a>
					</p>
					<table class="list">
						<tr class="title">
							<td>Field</td>
							<td>Value</td>
						</tr>
    <?php
    foreach($data AS $keyy => $value) {
      echo("<tr><td class=\"fieldname\">".htmlentities($keyy)."</td><td class=\"value\">".printValue($keyy,$value)."</td></tr>\n");
    }
    ?>
  </table>
					<p>
						<a
							href="<?php echo($_SERVER["PHP_SELF"]."?start=".urldecode($_GET["start"])."&number=".urldecode($_GET["number"])."&matchTermsExact=".$matchTermsExact."&termsOr=".$termsOr."&key=".$key.$urlSearchField.$urlFacetField) ?>">back</a>
					</p>
  <?php
} 
else {

  if(is_array($list) && (count($list)>0)) {    
    ?><h3>Found the following objects :</h3><?php
      echo("<p>".($start+1)." to ".($start+count($list))." of ".$total." objects</p>\n");
      echo("<p>");
      if($start>0) {
        echo("<a href=\"".$_SERVER["PHP_SELF"]."?start=".max(0,($start-$number))."&number=".$number."&matchTermsExact=".$matchTermsExact."&termsOr=".$termsOr."&key=".$key.$urlSearchField.$urlFacetField."\">previous ".min($start,$number)."</a>&nbsp;");
      } else {
        echo("previous 0&nbsp;");
      }
      if(($start+$number)<$total) {
        echo("<a href=\"".$_SERVER["PHP_SELF"]."?start=".($start+$number)."&number=".$number."&matchTermsExact=".$matchTermsExact."&termsOr=".$termsOr."&key=".$key.$urlSearchField.$urlFacetField."\">next ".min($number,($total-$start-$number))."</a>&nbsp;");
      } else {
        echo("next 0&nbsp;");
      }
      echo("</p>");
    ?> 
    <table class="list">
						<tr class="title">
							<td>Uri</td>
							<td>PrefLabel</td>
							<td>DocumentationProperties</td>
						</tr>
      <?php
      foreach($list AS $item) {
        echo("<tr>\n");
        echo("  <td><a href=\"".$_SERVER["PHP_SELF"]."?id=" .urlencode($item["uri"]) . "&start=".$start."&number=".$number."&matchTermsExact=".$matchTermsExact."&termsOr=".$termsOr."&key=".$key.$urlSearchField.$urlFacetField."\">" .htmlentities($item["uri"])."</a>&nbsp;");
        echo("  <td>".printValue("prefLabel",isset($item["prefLabel"])?$item["prefLabel"][0]:"")."</td>\n");
        echo("  <td>".printValue("DocumentationProperties",isset($item["DocumentationProperties"])?$item["DocumentationProperties"][0]:"")."</td>\n");
        echo("<tr>\n");
        
 
        
      }
      
      
      
      ?>
    <!--  
    </table>
    <table class="list">
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
  	echo("No objects were found for these search criteria.");
  }
  
  
  
}

?>
				</td>
						</tr>





						</form>

</body>
</html>
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

// Calls solr with search parameters and returns the result
function getList($start=0, $num=10, $config, $solrUrl) {	
	// build facet filter query part...
	$activeFacetFiltersMap = array();
	$activeFacetsFilterQueryString = "";
	foreach ($config["facetfields"] AS $item) {
		if(($item["type"]=="checkbox") || ($item["type"]=="radio")) {
			foreach($item["values"] AS $subitem) {
				if($subitem["value"] != "ALL" && $subitem["checked"]) {
					$activeFacetFiltersMap[$item["facet"]] = $subitem["value"];
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

	// build field filter query part...
	if (!isset($_GET["key"]) Or trim($_GET["key"]) == "")
		$searchTermsEntered = array();
	else
		$searchTermsEntered = explode(" ", trim($_GET["key"]));
	$exactTermMatching = (isset($_GET["matchTermsExact"]) && strcasecmp($_GET["matchTermsExact"], "false") == 0) ? false : true; // asterisk for searching part of terms..
	$allSearchFieldsQueryMap = array();
	foreach ($config["searchfields"] AS $item) {
		if($item["checked"]) {
			if (count($searchTermsEntered) > 0) { // search terms entered..
				$termsForField = array();
				foreach ($searchTermsEntered as $term ) {
					$termsForField[] = $term;
				}
				//$allSearchFieldsQueryMap[urldecode($item["field"])] = $termsForField;
				$allSearchFieldsQueryMap[urldecode(implode(",", $item["field"]))] = $termsForField;
			}
			else {
				$allSearchFieldsQueryMap[urldecode(implode(",", $item["field"]))] = array("*");
			}
		}
	}
	
	// assemble total query and use search terms...
	$orTerms = isset($_GET["termsOr"]) && strcasecmp($_GET["termsOr"], "false") == 0 ? "AND" : "OR"; // OR is default
	// build all search field filters query string..
	$allSearchFieldsQueryString = "";
	$queryFiltersTagName = "queryFiltersTag";
	
	// TODO configure a catch all field and set it up in SOLR also while indexing.
	$defaultSearchField = "DocumentationPropertiesText";
	
	$defaultSearchField1 = "*";
	$defaultSearchField2 = "LexicalLabelsPhrase";
	
	$queryLucenePrefix = "NOT_INITIALIZED";
	$rangeString = "&start=".intval($start) . "&rows=" . intval ( $num );
	$queryPostfix =  $activeFacetsFilterQueryString . $rangeString . $allFacetFieldsQueryString;
	// fill searchTermsQueryMap with default search value(s) if no search terms were entered...
	if (empty($allSearchFieldsQueryMap)) { // no search field filters activated
		if (!empty($searchTermsEntered)) { // search terms entered...
			$allSearchFieldsQueryMap[$defaultSearchField1] = $searchTermsEntered;
		}
		else { // no search fields selected and no search terms entered, search * in a strategic catch all field will be performed
			$allSearchFieldsQueryMap[$defaultSearchField1] = array("*");
		}
		
		$queryLucenePrefix = buildLuceneFilterQueryString($allSearchFieldsQueryMap, $orTerms, $exactTermMatching);
		
		$query = "fq={!tag=" . $queryFiltersTagName ."}" . $queryLucenePrefix . $queryPostfix;
	}
	else {
		$queryLucenePrefix = buildLuceneFilterQueryString($allSearchFieldsQueryMap, $orTerms, $exactTermMatching);
		$query = "fq={!tag=" . $queryFiltersTagName ."}" . $queryLucenePrefix . $queryPostfix;
		
	}

	/* Facet Queries :  */
	
	$activeFacetFilterMapString = "";
	foreach($activeFacetFiltersMap as $key => $value) {
		$activeFacetFilterMapString[] = $key . ":" . $value;
	}
	
	$facetQueries = array();
	$totalActiveFacetFilterMapString = empty($activeFacetFilterMapString) ? "" : " AND ". implode(" AND ", $activeFacetFilterMapString);
	//and/or search terms counts
	if (count($searchTermsEntered) > 0) { // if one or more term entered.. (not > 1 , because default search terms can already be more than one term..)
		$facetQueries[] = "{!ex=" . $queryFiltersTagName ." key=or}(" . buildLuceneFilterQueryString($allSearchFieldsQueryMap, "OR", $exactTermMatching) . ")" . $totalActiveFacetFilterMapString;
		$facetQueries[] = "{!ex=" . $queryFiltersTagName ." key=and}(". buildLuceneFilterQueryString($allSearchFieldsQueryMap, "AND", $exactTermMatching) . ")" . $totalActiveFacetFilterMapString;
	}	
	
	// match word exact/non exact
	if (count($searchTermsEntered) > 0) { // if one or more term entered..
		$facetQueries[] = "{!ex=" . $queryFiltersTagName ." key=matchExactWord}(" . buildLuceneFilterQueryString($allSearchFieldsQueryMap, $orTerms, true) . ")" . $totalActiveFacetFilterMapString;
		$facetQueries[] = "{!ex=" . $queryFiltersTagName ." key=matchPartOfWord}(". buildLuceneFilterQueryString($allSearchFieldsQueryMap, $orTerms, false) . ")" . $totalActiveFacetFilterMapString;
	}
	
	foreach($facetQueries AS $value) {
		$query .= "&facet.query=" . urlencode($value);
	}

	// debugging statement, handy to use for debugging generated queries
	//var_dump(urldecode("Query String ======: q=*:*&". $query));

	// Call SOLR
	if($response = solrRequest("q=*:*&" . $query, $solrUrl)) {
		if(isset($response["response"]) && isset($response["response"]["numFound"]) && isset($response["response"]["docs"]) && isset($response["facet_counts"]) && isset($response["facet_counts"]["facet_fields"])) {
			return array($response["response"]["docs"], intval($response["response"]["numFound"]), $response["facet_counts"]["facet_fields"], $response["facet_counts"]["facet_queries"]);
		}
	}


	return false;
}

function buildLuceneFilterQueryString($fieldsTermsMap, $isOrSearch, $exactWordMatching) {
	$allSearchFieldsQueryString = "";
	foreach($fieldsTermsMap AS $key => $values) {
		$fieldNames = explode(",", $key);
		$counter1 = 0;
		foreach($fieldNames as $fieldName) {
			$counter1++;
			$counter2 = 0;
			foreach($values as $term) {
				$counter2++;
				$allSearchFieldsQueryString .= "(" . $fieldName . ":" . (($exactWordMatching OR (!$exactWordMatching AND $term=="*")) ? "" : "*") . $term . (($exactWordMatching OR (!$exactWordMatching AND $term=="*")) ? "" : "*") . ")" .  ( (count($fieldNames) == $counter1) && (count($values) == $counter2) ? "" : $isOrSearch);
			}
		}
	}
	return $allSearchFieldsQueryString;
}

function buildSolrActiveFacetFiltersString($activeFacetFiltersMap) {
	$activeFacetsFilterQueryString = "";
	foreach($activeFacetFiltersMap as $key => $value) {
		$activeFacetsFilterQueryString .= "&fq={!tag=". $key . "}" . urlencode($key . ":" . $value);
	}
	return $activeFacetsFilterQueryString;
}

function buildLuceneQueryString($fieldsTermsMap, $isOrSearch, $exactWordMatching) {
	$allSearchFieldsQueryString = "";
	$counter1 = 0;
	foreach($fieldsTermsMap AS $key => $values) {
		$counter2 = 0;
		$counter1++;
		foreach($values as $term) {
			$counter2++; 			
			$allSearchFieldsQueryString .= "(" . $key . ":" . ($exactWordMatching ? "" : "*") . $term . ($exactWordMatching ? "" : "*") . ")" . ( (count($fieldsTermsMap) == $counter1) && (count($values) == $counter2) ? "" : $isOrSearch);
		}
	}
	return $allSearchFieldsQueryString;
}

function printValue($field,$value) {
	if(is_string($value)) {
  	if($value && in_array($field, array("uri","inScheme","inSkosCollection","hasTopConcept","topConceptOf"))) { // uitbreiden als er meer verwijs velden bij komen..
      return "<a href=\""."http://".$_SERVER["HTTP_HOST"]."/api/concept?id=".$value."\">".htmlentities("".$value."")."</a>";
    } else {
      return(htmlentities($value, ENT_COMPAT, "UTF-8")); // | ENT_HTML401, "UTF-8"));
    }
  } else if(is_int($value)) {
    return(htmlentities($value)); 
  } else if(is_bool($value)) {
    return $value?"TRUE":"FALSE";
  } else if(is_array($value)) {
    $text = "<table>\n";
    foreach($value AS $subkey => $subvalue) {
      $text.="<tr><td class=\"item\">".printValue($field,$subvalue)."</td></tr>\n";
    } 
    $text.= "</table>\n";
    return $text;
  } else {
    return "<i>ONBEKEND TYPE (veld ".htmlentities($field).")</i>";
  }
}

function solrRequest($query, $solr_baseurl) {
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

function solrEscape($text) {
  $match = array('\\', '+', '-', '&', '|', '!', '(', ')', '{', '}', '[', ']', '^', '~', '*', '?', ':', '"', ';', ' ');
  $replace = array('\\\\', '\\+', '\\-', '\\&', '\\|', '\\!', '\\(', '\\)', '\\{', '\\}', '\\[', '\\]', '\\^', '\\~', '\\*', '\\?', '\\:', '\\"', '\\;', '\\ ');
  $text = str_replace($match, $replace, $text);
  if(!preg_match("/ /",$text)) {
    $string = "\"".$text."\"";
  }
  return $text;
}


?>