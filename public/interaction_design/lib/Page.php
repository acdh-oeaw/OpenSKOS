<?php

define('DS', '/');

class Page
{
    private $theme = 'clean'; // 'clean' of 'preview'
    private $menu = array();
    private $titleBase = 'Titel';
    private $titleValue = '';
    private $contentLeft;
    private $contentRight;
    private $breadcrumb = array();
    private $tabs = array();
    private $subtabs = array();
    private $template = 'main';
    private $layout = 'a';
    private $bodyOnly = false;
    

    // Drie vaste niveaus, deze moeten in de menu-configuratie staan en worden automatisch in het kruimelpad opgenomen.
    private $n1 = '';   // 'Applicatie'
    private $n2 = '';   // 'Sectie' (hoofdmenu item)
    private $n3 = '';   // 'Module' (submenu item)
    private $sub = '';  // 'Sub'
    
    function __construct() {

        
        // Load menu config
        require_once dirname(__FILE__) . '/../menu.php';
        $this->menu = $menu;

/*
        echo '<pre>';
        print_r($menu);
        echo '</pre>';
*/
        // Set 'n1', 'n2' and 'n3' and check if they are available in the config
        if (!empty($_GET['n1'])) {
            $this->n1 = $_GET['n1'];
            if (!isset($this->menu[$this->n1]['_label'])) {
                exit('Niveau 1 \'' . $this->n1 . '\' niet bekend in menu configuratie.');
            } elseif (!empty($_GET['n2'])) {
                $this->n2 = $_GET['n2'];
                if (!isset($this->menu[$this->n1][$this->n2]['_label'])) {
                    exit('Niveau 2 \'' . $this->n2 . '\' niet bekend in menu configuratie.');
                } elseif (!empty($_GET['n3'])) {
                    $this->n3 = $_GET['n3'];
                    if (!isset($this->menu[$this->n1][$this->n2][$this->n3]['_label'])) {
                        exit('Niveau 3 \'' . $this->n3 . '\' niet bekend in menu configuratie.');
                    }
                }
            }
        }

        // Set 'sub'. Filename must be blah.sub.php
        if (!empty($_GET['sub'])) {
            $this->sub = $_GET['sub'];
        }

        // Create filepath
        $filepath = 'pages';
        if(empty($this->n1)) {
            // Geen applicatie gespecificeerd: toon de portal
            $filepath .= DS . 'index';
        } else {
            // Wel applicatie gespecificeerd: kies juiste subdirectory
            $filepath .= DS . $this->n1;
            if(count($this->menu[$this->n1]) <= 1) {
                // Geen tabs aanwezig: toon de index
                $filepath .= DS . 'index';
            } else {
                // Wel tabs aanwezig
                if(empty($this->n2)) {
                    // Geen tab gespecificeerd: neem de eerste
                    $n2Keys = array_keys($this->menu[$this->n1]);
                    $this->n2 = $n2Keys[1];
                }
                $filepath .= DS . $this->n2;
                if(count($this->menu[$this->n1][$this->n2]) > 1) {
                    // Subtabs aanwezig
                    if(empty($this->n3)) {
                        // Geen subtab gespecificeerd: neem de eerste
                        $n3Keys = array_keys($this->menu[$this->n1][$this->n2]);
                        $this->n3 = $n3Keys[1];
                    }
                    $filepath .= '-' . $this->n3;
                }
            }
        }

        // Add subpage and extension
        if(!empty($this->sub)) {
            $filepath .= '.' . $this->sub;
        }
        $filepath .= '.php';

        // Create tabs
        if(!($this->n1 == 'algemeen' && $this->n2 == 'aanmelden')) {
            if(isset($this->menu[$this->n1])) { 
                if(count($this->menu[$this->n1]) > 1) {
                    foreach($this->menu[$this->n1] as $k => $v) {
                        if($k != '_label') {
                            $this->tabs[] = array(
                                'label'     => $v['_label'],
                                'href'      => '?n1=' . $this->n1 . '&amp;n2=' . $k,
                                'current'   => ($this->n2 == $k)
                            );
                        }
                    }
                }
            }
        }

        // Create subtabs
        if(!($this->n1 == 'algemeen' && $this->n2 == 'aanmelden')) {
            if(isset($this->menu[$this->n1][$this->n2])) {
                if(count($this->menu[$this->n1][$this->n2]) > 1) {
                    foreach($this->menu[$this->n1][$this->n2] as $k => $v) {
                        if($k != '_label') {
                            $this->subtabs[] = array(
                                'label'     => $v['_label'],
                                'href'      => '?n1=' . $this->n1 . '&amp;n2=' . $this->n2 . '&amp;n3=' . $k,
                                'current'   => ($this->n3 == $k)
                            );
                        }
                    }
                }
            }
        }

        // Create breadcrumb
        $this->addToBreadcrumb('OpenSKOS', '?', true);
        if($this->n1) {
            if($this->n1 != 'algemeen') {
                if($this->n1) $this->addToBreadcrumb($this->menu[$this->n1]['_label'], '?n1=' . $this->n1, true);
                if($this->n2) $this->addToBreadcrumb($this->menu[$this->n1][$this->n2]['_label'], '?n1=' . $this->n1 . '&amp;n2=' . $this->n2, true);
                if($this->n3) $this->addToBreadcrumb($this->menu[$this->n1][$this->n2][$this->n3]['_label'], '?n1=' . $this->n1 . '&amp;n2=' . $this->n2 . '&amp;n3=' . $this->n3, true);
            }
        }
        
        // Include file
        if(file_exists($filepath)) {
            require_once (getcwd().'/'.$filepath);    
        } else {
            exit('Bestand niet gevonden:<br />' . $filepath);
            
        }

        // Output the page
        
        $this->output();
        
        

    }

    private function setTitle($base, $value = '') {
        
        $this->titleBase = $base;
        if(!empty($value)) $this->titleValue = $value;

    }    

    
    private function setContent($content1 = null, $content2 = null, $content3 = null, $content4 = null, $content5 = null) {
        
        $this->content1 = empty($content1) ? '' : $content1;
        $this->content2 = empty($content2) ? '' : $content2;
        $this->content3 = empty($content3) ? '' : $content3;
        $this->content4 = empty($content4) ? '' : $content4;
        $this->content5 = empty($content5) ? '' : $content5;

    } 
    
    
    public function setTemplate($template, $layout = 'a', $bodyOnly = false) {
        
        $this->bodyOnly = $bodyOnly;
        $this->layout   = $layout;
        $this->template = $template;


    } 
    
    private function output() {

        if(!$this->bodyOnly) {
            $this->outputTemplateStart();
        }
        require_once getcwd().'/themes/' . $this->theme . '/' . $this->template . '.php';
        if(!$this->bodyOnly) {
            $this->outputTemplateEnd();
        }
        
    }

    
    private function outputTemplateStart() {
    
        ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <title><?php echo sprintf($this->titleBase, $this->titleValue); ?></title>
        <link rel="stylesheet" type="text/css" href="<?php echo 'themes/' . $this->theme; ?>/css/main.css" />
        <script language="JavaScript" type="text/javascript" src="js/mootools.js"></script>
        <script language="JavaScript" type="text/javascript" src="js/main.js"></script>
        <script language="JavaScript" type="text/javascript">
        
            var page = '<?php echo $this->getPage(); ?>';
            var theme = '<?php echo $this->theme; ?>';
            var layout = '<?php echo $this->layout; ?>';
            var modalboxSize = 'fill';
        
        </script>
    </head>
    <body>
    
<?php
        
    }

    private function outputTemplateEnd() {
    
        ?>        

    </body>
</html>
<?php
        
    }

    
    
    
    public function addToBreadcrumb($title, $href, $ismenuitem = false) {
    
        if(strpos($title, '(') !== false) {
            $title = substr($title, 0, strpos($title, '('));    
        }
        $this->breadcrumb[] = array($title, $href, $ismenuitem);
        
    }

    
    public function getPage() { 

        $n1     = empty($_GET['n1']) ? '' : $_GET['n1'];
        $n2     = empty($_GET['n2']) ? '' : $_GET['n2'];
        $n3     = empty($_GET['n3']) ? '' : $_GET['n3'];
        $sub    = empty($_GET['sub']) ? '' : $_GET['sub'];
        $page   = 'n1=' . $n1 . '&n2=' . $n2 . '&n3=' . $n3 . '&sub=' . $sub;
        $page   = urlencode($page); // Required to make a valid name
        return $page;

    }

    
    public function getContentHtml($prefix, $contents) {
        
        // Set current
        $page = $this->getPage();
        if(!empty($_SESSION['content'][$page][$prefix])) {
            $current = $_SESSION['content'][$page][$prefix];
        } else {
            $current = ''; 
        }
        
        // Build HTML
        $containerId = 'content_' . $prefix;
        $s = '<div id="' . $containerId . '">';
        foreach($contents as $key => $content) {
            $id = $containerId . '_' . $key;
            $display = ($key == $current) ? 'block' : 'none';
            $s .= '<div id="' . $id . '" style="display: ' . $display . ';">' . $content . '</div>';
        }
        $s .= '</div>';
        
        return $s;
        
    }


    public function getPaneltabHtml($prefix, $paneltabs, $editable = false) {

        // Set current
        $page = $this->getPage();
        if(!empty($_SESSION['paneltab'][$page][$prefix])) {
            $current = $_SESSION['paneltab'][$page][$prefix];
        } else {
            $a = array_keys($paneltabs);
            $current = $a[0];
        }
        
        // Build HTML
        $containerId = 'paneltab_' . $prefix;
        $s  = '<div class="paneltabs" id="' . $containerId . '">';
        $s .= '<ul class="tabs">';
        foreach($paneltabs as $key => $paneltab) {
            $id = $containerId . '_tab_' . $key;
            $class = ($key == $current) ? 'huidig' : '';
            $remove = ($editable) ? '<img class="remove_tab" src="images/icons/verwijder.gif" alt="" title="Remove" />' : '';
            $s .= '<li id="' . $id . '" class="' . $class . '"><a style="background-image: url(images/icons/' . $paneltab['icon'] . '.gif);" href="#" onclick="setPaneltab(\'' . $prefix . '\', \'' . $key . '\'); return false;">' . $paneltab['label'] . '</a>' . $remove . '</li>';
        }
        if($editable) {
            $s .= '<li class="action"><a href="#" class="clickable"><img src="images/icons/nieuw.gif" alt="" title="Add" /></a></li>';
        }        
        $s .= '</ul>';
        $s .= '<div class="contents">';
        foreach($paneltabs as $key => $paneltab) {
            $id = $containerId . '_content_' . $key;
            $display = ($key == $current) ? 'block' : 'none';
            $s .= '<div id="' . $id . '" style="display: ' . $display . '" >' . $paneltab['content'] . '</div>';
        }
        $s .= '</div>';
        $s .= '</div>';
        
        return $s;

    }

    
    public function getGridHtml($prefix, $gridColumns, $gridActions, $gridData, $gridOptions, $actions = array()) {

        $rowstotal              = isset($gridOptions['rowstotal'])              ? $gridOptions['rowstotal']             : count($gridData);
        $rowsperpage            = isset($gridOptions['rowsperpage'])            ? $gridOptions['rowsperpage']           : 10;
        $showcheckboxes         = isset($gridOptions['showcheckboxes'])         ? $gridOptions['showcheckboxes']        : true;  
        $shownumbers            = isset($gridOptions['shownumbers'])            ? $gridOptions['shownumbers']           : true;
        $showpager              = isset($gridOptions['showpager'])              ? $gridOptions['showpager']             : true;
        $modes                  = isset($gridOptions['modes'])                  ? $gridOptions['modes']                 : array('lijst', 'galerij', 'grid');
        $detailmediatype        = isset($gridOptions['detailmediatype'])        ? $gridOptions['detailmediatype']       : 'topview';
        $showfilters            = isset($gridOptions['showfilters'])            ? $gridOptions['showfilters']           : true;
        $actionsmodesposition   = isset($gridOptions['actionsmodesposition'])   ? $gridOptions['actionsmodesposition']  : array('above', 'below');
        $showhead               = isset($gridOptions['showhead'])               ? $gridOptions['showhead']              : true;
        $rowaction              = isset($gridOptions['rowaction'])              ? $gridOptions['rowaction']             : null;

        $pagestotal     = ceil($rowstotal/$rowsperpage);
        $rowsthispage   = ($rowsperpage < $rowstotal) ? $rowsperpage : $rowstotal;
        $containerId    = 'grid_' . $prefix;
        $s = '<div id="' . $containerId . '" class="grid">';

        // Set current
        $page = $this->getPage();
        if(!empty($_SESSION['grid'][$page][$prefix])) {
            $mode = $_SESSION['grid'][$page][$prefix];
        } else {
            $mode = $modes[0];
        }

        
//        echo '<pre>';
//        print_r($_SESSION);
//        echo '</pre>';
//        exit;

        
        // Display filter?
        $filters = '';
        if($showfilters) {
            $styleSimple = 'block';
            $styleAdvanced = 'none';
            if(isset($_SESSION['filter'][$page][$prefix])) {
                if($_SESSION['filter'][$page][$prefix] == 'advanced') { 
                    $styleSimple = 'none';
                    $styleAdvanced = 'block';
                }
            }
            $filters = '
            
            <form class="filter" id="filter_' . $prefix . '_simple" onSubmit="applySimpleFilter(\'' . $prefix . '\'); return false;" style="display: ' . $styleSimple . ';">
                <input type="submit" style="display: none;" />
                <table>
                    <tbody>
                        <tr>
                            <td><input class="filter_waarde" type="text" value="" size="64" style="width: auto;" /></td>
                            <td>
                                <a href="#" class="filter_toepassen"            title="Filter toepassen" onclick="applySimpleFilter(\'' . $prefix . '\'); return false;"><img src="images/icons/filter_toepassen_clickable.gif" alt="Filter toepassen" /></a>
                                <a href="#" class="filter_verwijderen disabled" title="Filter verwijderen" ><img src="images/icons/verwijder.gif" alt="Filter verwijderen" /></a>
                                &nbsp;&nbsp;&nbsp;
                                <a href="#" class="filter_toggle"               title="Toon geavanceerd filter" onclick="toggleFilterMode(\'' . $prefix . '\'); return false;"><img src="images/icons/uitklappen_clickable.gif" alt="Toon geavanceerd filter" /></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>

            
            <form class="filter" id="filter_' . $prefix . '_advanced" style="display: ' . $styleAdvanced . ';">
                <input type="submit" style="display: none;" />

                <table class="actions">
                    <tr>
                        <td>    
			                <a href="#" class="filter_toepassen" title="Filter toepassen" onclick="return false;"><img src="images/icons/filter_toepassen_clickable.gif" alt="Filter toepassen" /></a>
			                <a href="#" class="filter_verwijderen" title="Filter verwijderen" onclick="return false;"><img src="images/icons/verwijder_clickable.gif" alt="Filter verwijderen" /></a>
			                &nbsp;&nbsp;&nbsp;
			                <a href="#" class="filter_openen" title="Filter openen" onclick="openFilter(\'' . $prefix . '\'); return false;" ><img src="images/icons/openen_clickable.gif" alt="Filter openen" /></a>
			                <a href="#" class="filter_opslaan" title="Filter opslaan" onclick="saveFilter(\'' . $prefix . '\'); return false;"><img src="images/icons/opslaan_clickable.gif" alt="Filter opslaan" /></a>
			                &nbsp;&nbsp;&nbsp;
			                <a href="#" title="Toon eenvoudig filter" onclick="toggleFilterMode(\'' . $prefix . '\'); return false;"><img src="images/icons/inklappen_clickable.gif" alt="Toon eenvoudig filter" /></a>
	                   </td>
	                </tr>
                </table>
                
                
                
				<script type="text/javascript">
					window.addEvent(\'domready\', function() {
					    initAdvancedFilter(\'' . $prefix . '\');
					});
				</script>
                <table>
                    <tbody class="set" style="display: none;">
                        <tr class="set_mode" style="display: none;">
                            <td colspan="5"><select style="width: auto;"><option>versmal</option><option>verbreed</option></select> het resultaat met</td>
                        </tr>
                        <tr class="set_header">
                            <th>Set 1</th>
                            <td colspan="4">
                                combineer regels in deze set met <select onchange="updateFilterCombineMethod(\'' . $prefix . '\', this);" style="width: auto;"><option value="en">en</option><option value="of">of</option></select>
                                &nbsp;&nbsp;&nbsp;
                                <a href="#" class="filterset_toevoegen"   title="Set toevoegen"     onclick="addFilterSet(\'' . $prefix . '\'); return false;"><img src="images/icons/nieuw_clickable.gif" alt="Set toevoegen" /></a>
                                <a href="#" class="filterset_verwijderen" title="Set verwijderen"   onclick="removeFilterSet(\'' . $prefix . '\', this); return false;"><img src="images/icons/verwijder_clickable.gif" alt="Set verwijderen" /></a>
                            </td>
                        </tr>
                        <tr class="set_rule">
                            <td></td>
                            <td style="width: 200px;"><select name="veld" onchange="updateFilterRuleType(\'' . $prefix . '\', this);"><option value="veld1">Voorbeeldveld 1 (string)</option><option value="veld2">Voorbeeldveld 1 (boolean)</option></select></td>
                            <td style="width: 100px;">
                                <select name="voorwaarde">
                                    <option value="bevat">bevat</option>
                                    <option value="bevatniet">bevat niet</option>
                                    <option value="isgelijkaan">is gelijk aan</option>
                                    <option value="isnietgelijkaan">is niet gelijk aan</option>
                                </select>
                                <select name="voorwaarde" style="display: none;" readonly="readonly">
                                    <option value="isgelijkaan">is gelijk aan</option>
                                </select>
                            </td>
                            <td style="width: 150px;">
                                <input name="waarde" type="text" value="" />
                                <input name="waarde" type="checkbox" value="" style="display: none;" />
                            </td>
                            <td>
                                <a href="#" class="filterwaarde_kiezen"     title="Waarde kiezen"       onclick="openModalbox(\'?n1=collectiebeheer&n2=verzamelingen&sub=modalbox_picklist_filterwaarde\', \'single\'); return false;"><img src="images/icons/picklist_clickable.gif" alt="Waarde kiezen" /></a>
                                <a href="#" class="filterregel_toevoegen"   title="Regel toevoegen"     onclick="addFilterRule(\'' . $prefix . '\', this); return false;"><img src="images/icons/nieuw_clickable.gif" alt="Regel toevoegen" /></a>    
                                <a href="#" class="filterregel_verwijderen" title="Regel verwijderen"   onclick="removeFilterRule(\'' . $prefix . '\', this); return false;"><img src="images/icons/verwijder_clickable.gif" alt="Regel verwijderen" /></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>';
         
            
        }
        
        // =============
        // Mode 'detail'
        // =============

        if(in_array('detail', $modes)) {
            
            $display = ($mode == 'detail') ? 'block' : 'none';
            $s .= '<div id="' . $containerId . '_detail" class="grid_detail" style="display: ' . $display . '">';
            $s .= $filters;
            if($showpager) $s .= $this->getPagerHtml($rowstotal, 1, $rowstotal);
            if((count($actions)>0 || count($modes)>1) && in_array('above', $actionsmodesposition)) $s .= $this->getActionHtml($actions, $modes, $prefix);
            
            if($detailmediatype == 'topview') {
                $s .= '<div class="topview_container" style="background-image: url(images/topview/' . $gridOptions['thumbkey'] . '.jpg);"><div class="topview_toolbar"><img src="themes/' . $this->theme . '/images/topview_toolbar.gif" alt="" /></div></div>';
            } else {
                $s .= '
<script type="text/javascript" src="js/swfobject.js"></script>
<script type="text/javascript">
window.addEvent(\'domready\', function() {
  var so = new SWFObject(\'swf/jwplayer.swf\',\'kors\',\'478\',\'270\',\'9.0.115\');
  so.addParam(\'allowfullscreen\',\'true\');
  so.addParam(\'allowscriptaccess\',\'true\');
  so.addParam(\'flashvars\',\'streamer=rtmp://xenon.pictura-hosting.nl/mediastreamer&file=pdp/kors_kiest_pictura_streaming.mp4&type=rtmp&autostart=true&controlbar=over\');
  so.write(\'videoplayer\');
});
</script>
<div class="video_container" id="videoplayer"></div>                
                ';
            }

            $filedetails = '
    <dl class="filedetails">
        <dt>Bestandsnaam</dt>
        <dd>kvb001000001.jpg</dd>
        <dt>Datum</dt>
        <dd>16-05-2009 10:15:35</dd>
        <dt>Gebruiker</dt>
        <dd>Mark Lindeman</dd>
        <dt>Grootte</dt>
        <dd>0.03 MB</dd>
        <dt>Mimetype</dt>
        <dd>image/jpeg</dd>
        <dt>Bewerkt</dt>
        <dd>nee</dd>
    </dl>
            ';
            
            $j = 0;
            $s .= '<table class="toolbar"><tr><td class="left" nowrap="nowrap">';
            foreach($gridActions as $k => $v) {
                $href = empty($v['href']) ? '' : $v['href'];
                $title = empty($v['label']) ? '' : $v['label'];
                if(!empty($href)) {
                    $class = 'clickable';
                    $postfix = '_clickable';
                } else {
                    $class = '';
                    $postfix = '';
                }
                $s .= ' <a title="' . $title . '" href="' . $href . '"><img alt="' . $title . '" class="' . $class . '" src="images/icons/' . $k . $postfix . '.gif" /></a>';
                $j++;
            }
            $s .= '</td><td class="right">' . $filedetails. '</td></tr></table>';
            if((count($actions)>0 || count($modes)>1) && in_array('below', $actionsmodesposition)) $s .= $this->getActionHtml($actions, $modes, $prefix);
            if($showpager) $s .= $this->getPagerHtml($rowstotal, 1, $rowstotal);
            $s .= '</div>';
            
        }
        
        
        // ============
        // Mode 'lijst'
        // ============

        if(in_array('lijst', $modes)) {
            $display = ($mode == 'lijst') ? 'block' : 'none';
            $s .= '<div id="' . $containerId . '_lijst" class="grid_lijst" style="display: ' . $display . '">';
            $s .= $filters;
            if($showpager) $s .= $this->getPagerHtml($rowstotal, $rowsperpage, $pagestotal);

            if((count($actions)>0 || count($modes)>1) && in_array('above', $actionsmodesposition)) $s .= $this->getActionHtml($actions, $modes, $prefix);
            
            if($rowstotal < 1) {
                $s .= '<p>No results found.</p>';
            } else {
                $s .= '<div class="tablecontainer"><table class="gridtable">';
                
                if($showhead) {
	                $s .= '<thead><tr>';
	                if ($rowstotal > 0 && $showcheckboxes) $s .= '<th><input type="checkbox" /></th>';
	                if ($rowstotal > 0 && $shownumbers) $s .= '<th></th>';
	                $i = 0;
	                foreach($gridColumns as $k => $v) {
	                	$style = empty($v['style']) ? '' : ' style="' . $v['style'] . '"';
	                    $i++;
	                    $label = empty($v['label']) ? '' : $v['label'];
	                    $s .= '<th' . $style . '>' . $label . '</th>';
	                }
	                if(count($gridActions) > 0) {
	                    $s .= '<th class="actions" colspan="' . count($gridActions) . '"></th>';
	                }
	                $s .= '</tr>';
	                $s .= '</thead>';
                }
                
                $s .= '<tbody>';
                for($i = 0; $i < $rowsthispage; $i++) {
                    $class = ($i%2 == 0) ? 'odd' : 'even';
                    $onclick = empty($rowaction) ? '' : ' style="cursor: pointer;" title="' . $rowaction['label'] . '" onclick="' . $rowaction['onclick'] . '"';
                    $s .= '<tr class="' . $class . '">';
                    if ($rowstotal > 0 && $showcheckboxes) $s .= '<td class="checkbox"><input type="checkbox" /></td>';
                    if ($rowstotal > 0 && $shownumbers) $s .= '<td class="number">' . ($i + 1) . '</td>';
                    $j = 0;
                    foreach($gridColumns as $k => $v) {
                    	if(isset($gridData[$i][$j])) {
                            if(is_bool($gridData[$i][$j])) {
	                            // $imagename = $gridData[$i][$j] ? 'toggle_true' : 'toggle_false';
	                            // $cellcontent = '<img src="images/icons/' . $imagename . '.gif">';
	                            $checked = $gridData[$i][$j] ? ' checked="checked"' : '';
	                            $cellcontent = '<input type="checkbox"' . $checked . '>';
	                        } else {
	                            $cellcontent = empty($gridData[$i][$j]) ? $v['default'] : $gridData[$i][$j];
	                        }
                    	} else {
                    		$cellcontent = empty($gridData[$i][$j]) ? $v['default'] : $gridData[$i][$j];
                    	}
                        $style = empty($v['style']) ? '' : ' style="' . $v['style'] . '"'; 
                        $s .= '<td' . $style . $onclick . '>' . $cellcontent . '</td>';
                        $j++;
                    }
                    $j = 0;
                    foreach($gridActions as $k => $v) {
                        $href = empty($v['href']) ? '' : $v['href'];
                        $title = empty($v['label']) ? '' : $v['label'];
                        if($i == 0 && !empty($href)) {
                            $class = 'clickable';
                            $postfix = '_clickable';
                        } else {
                            $class = '';
                            $postfix = '';
                        }
                        $s .= ' <td class="action"><a title="' . $title . '" href="' . $href . '"><img alt="' . $title . '" class="' . $class . '" src="images/icons/' . $k . $postfix . '.gif" /></a></td>';
                        $j++;
                    }
                    $s .= '</tr>';
                }
                $s .= '</tbody>';
                $s .= '</table>';
                $s .= '</div>';
            }
            
            

            if((count($actions)>0 || count($modes)>1) && in_array('below', $actionsmodesposition)) $s .= $this->getActionHtml($actions, $modes, $prefix);
            if($showpager) $s .= $this->getPagerHtml($rowstotal, $rowsperpage, $pagestotal);
            $s .= '</div>';
        }

        
        // ==============
        // Mode 'galerij'
        // ==============
        
        if(in_array('galerij', $modes)) {
            $display = ($mode == 'galerij') ? 'block' : 'none';
            $s .= '<div id="' . $containerId . '_galerij" class="grid_galerij" style="display: ' . $display . '">';
            $s .= $filters;
            if($showpager) $s .= $this->getPagerHtml($rowstotal, $rowsperpage, $pagestotal);
            if((count($actions)>0 || count($modes)>1) && in_array('above', $actionsmodesposition)) $s .= $this->getActionHtml($actions, $modes, $prefix);
            if($rowstotal < 1) {
                $s .= '<p>No results found.</p>';
            } else {
                for($i = 0; $i < $rowsthispage; $i++) {
                    $src = 'images/thumbs_galerij/' . $gridOptions['thumbkey'] . '_' . ($i + 1) . '.jpg';
                    $srcDefault = 'images/thumbs_galerij/' . $gridOptions['thumbkey'] . '_default.jpg';
                    $src = file_exists($src) ? $src : $srcDefault;
                    $img = '<img src="' . $src . '" alt="" />';
                    $class = '';
                    $title = '';

                    // Set action on the thumbnail?
                    if(!empty($gridActions)) {
                        $actionKeys = array_keys($gridActions);
                        $firstActionKey = $actionKeys[0];
                        if($i == 0 && !empty($gridActions[$firstActionKey]['href'])) {
                            $href = $gridActions[$firstActionKey]['href'];
                            $class = ' clickable';
                            $img = '<a href="' . $href . '">' . $img . '</a>';
                        }
                        if(!empty($gridActions[$firstActionKey]['label'])) {
                            $title = $gridActions[$firstActionKey]['label'];
                        }
                    }
                    
                    // Create toolbar?
                    if(!$showcheckboxes && empty($gridActions)) {
                        $toolbar = '';                    
                    } else {
                        $toolbar = '<tr><td class="toolbar">';
                        if($showcheckboxes) {
                            $toolbar .= '<input type="checkbox" />';
                        }
                        $a = array_reverse($gridActions);
                        foreach($a as $k => $v) {
                            $iconhref = empty($v['href']) ? '' : $v['href'];
                            $icontitle = empty($v['label']) ? '' : $v['label'];
                            if($i == 0 && !empty($href)) {
                                $iconclass = ' clickable';
                                $iconpostfix = '_clickable';
                            } else {
                                $iconclass = '';
                                $iconpostfix = '';
                            }
                            $toolbar .= '<a title="' . $icontitle . '" href="' . $iconhref . '"><img alt="' . $icontitle . '" class="' . $iconclass . '" src="images/icons/' . $k . $iconpostfix . '.gif" /></a>';
                        }
                        $toolbar .= '</td></tr>';
                    }
                    
                    // Combine elements
                    $s .= '<div><table><tr><td class="thumb' . $class . '" title="' . $title . '">' . $img . '</td></tr>' . $toolbar . '</table></div>';
                }
            }
            if((count($actions)>0 || count($modes)>1) && in_array('below', $actionsmodesposition)) $s .= $this->getActionHtml($actions, $modes, $prefix);
            if($showpager) $s .= $this->getPagerHtml($rowstotal, $rowsperpage, $pagestotal);
            $s .= '</div>';
        }

        
        // ===========
        // Mode 'grid'
        // ===========
        
        if(in_array('grid', $modes)) {
            $display = ($mode == 'grid') ? 'block' : 'none';
            $s .= '<div id="' . $containerId . '_grid" class="grid_grid" style="display: ' . $display . '">';
            $s .= $filters;
            if($showpager) $s .= $this->getPagerHtml($rowstotal, $rowsperpage, $pagestotal);
            if((count($actions)>0 || count($modes)>1) && in_array('above', $actionsmodesposition)) $s .= $this->getActionHtml($actions, $modes, $prefix);
            if($rowstotal < 1) {
                $s .= '<p>No results found.</p>';
            } else {
                $toolbar = $showcheckboxes ? '<tr><td class="toolbar"><input type="checkbox" /></td></tr>' : '';
                for($i = 0; $i < $rowsthispage; $i++) {
                    $src = 'images/thumbs_grid/' . $gridOptions['thumbkey'] . '_' . ($i + 1) . '.jpg';
                    $srcDefault = 'images/thumbs_grid/' . $gridOptions['thumbkey'] . '_default.jpg';
                    $src = file_exists($src) ? $src : $srcDefault;
                    $img = '<img src="' . $src . '" alt="" />';
                    $class = '';
                    $title = '';
                    
                    // Set action on the thumbnail?
                    if(!empty($gridActions)) {
                        $actionKeys = array_keys($gridActions);
                        $firstActionKey = $actionKeys[0];
                        if($i == 0 && !empty($gridActions[$firstActionKey]['href'])) {
                            $href = $gridActions[$firstActionKey]['href'];
                            $class = ' clickable';
                            $img = '<a href="' . $href . '">' . $img . '</a>';
                        }
                        if(!empty($gridActions[$firstActionKey]['label'])) {
                            $title = $gridActions[$firstActionKey]['label'];
                        }
                    }
                    
                    // Create toolbar?
                    if(!$showcheckboxes && empty($gridActions)) {
                        $toolbar = '';                    
                    } else {
                        $toolbar = '<tr><td class="toolbar">';
                        if($showcheckboxes) {
                            $toolbar .= '<input type="checkbox" />';
                        }
                        $a = array_reverse($gridActions);
                        foreach($a as $k => $v) {
                            $iconhref = empty($v['href']) ? '' : $v['href'];
                            $icontitle = empty($v['label']) ? '' : $v['label'];
                            if($i == 0 && !empty($href)) {
                                $iconclass = ' clickable';
                                $iconpostfix = '_clickable';
                            } else {
                                $iconclass = '';
                                $iconpostfix = '';
                            }
                            $toolbar .= '<a title="' . $icontitle . '" href="' . $iconhref . '"><img alt="' . $icontitle . '" class="' . $iconclass . '" src="images/icons/' . $k . $iconpostfix . '.gif" /></a>';
                        }
                        $toolbar .= '</td></tr>';
                    }

                    // Combine elements
                    $s .= '<div><table><tr><td class="thumb' . $class . '" title="' . $title . '">' . $img . '</td></tr>' . $toolbar . '</table></div>';
                    
                }
            }
            if((count($actions)>0 || count($modes)>1) && in_array('below', $actionsmodesposition)) $s .= $this->getActionHtml($actions, $modes, $prefix);
            if($showpager) $s .= $this->getPagerHtml($rowstotal, $rowsperpage, $pagestotal);
            $s .= '</div>';
        }
        
        
        
        $s .= '</div>';
        return $s;
        
    }
    
    public function getPagerHtml($rowstotal = 100, $rowsperpage = 10, $pagestotal = 1) {

        if($rowsperpage > 1) {
            $s  = '<table class="pager">' . PHP_EOL;
            $s .= '    <tr>' . PHP_EOL;
            $s .= '        <td class="left"><strong>' . $this->formatInteger($rowstotal) . '</strong>&nbsp;totaal&nbsp;&nbsp;&sdot;&nbsp;&nbsp;<input type="text" size="2" class="number" name="aantalperpagina" value="' . $rowsperpage . '" />&nbsp;per&nbsp;pagina</td>' . PHP_EOL;
            if($pagestotal > 1) {
                $s .= '        <td class="right"><img src="images/icons/pager_eerste.gif" alt="" />&nbsp;<img src="images/icons/pager_vorige.gif" alt="" />&nbsp;&nbsp;pagina&nbsp;<input type="text" value="1" size="2" class="number" name="huidigepagina" />&nbsp;van&nbsp;<strong>' . $this->formatInteger($pagestotal) . '</strong>&nbsp;&nbsp;<img src="images/icons/pager_volgende.gif" alt="" />&nbsp;<img src="images/icons/pager_laatste.gif" alt="" /></td>' . PHP_EOL;
            }
            $s .= '    </tr>' . PHP_EOL;
            $s .= '</table>' . PHP_EOL;
        } else {
            $s  = '<table class="pager fullwidth">' . PHP_EOL;
            $s .= '    <tr>' . PHP_EOL;
            $s .= '        <td class="left"><img src="images/icons/pager_eerste.gif" alt="" />&nbsp;<img src="images/icons/pager_vorige.gif" alt="" /></td>' . PHP_EOL;
            $s .= '        <td class="center"><input type="text" value="1" size="2" class="number" />&nbsp;van&nbsp;<strong>' . $this->formatInteger($pagestotal) . '</strong></td>' . PHP_EOL;
            $s .= '        <td class="right"><img src="images/icons/pager_volgende.gif" alt="" />&nbsp;<img src="images/icons/pager_laatste.gif" alt="" /></td>' . PHP_EOL;
            $s .= '    </tr>' . PHP_EOL;
            $s .= '</table>' . PHP_EOL;
        }
        return $s;
        
    }
    
    
    public function getActionHtml($actions = array(), $modes = array(), $prefix = '', $label = true) {

        $s = '';
        if(count($actions)>0 || count($modes)>1) {
            
        	$s .= '<table class="actions"><tr>';
            
            if(count($actions) > 0) {
                $s .= '<td class="left"><ul>';
                foreach($actions as $key => $action) {
                    $href = empty($action['href']) ? '' : $action['href'];
                    $title = empty($action['label']) ? '' : $action['label'];
                    if(!empty($href)) {
                        $class = 'clickable';
                        $postfix = '_clickable';
                    } else {
                        $class = '';
                        $postfix = '';
                    }
                    $labelElement = $label ? '<span>' . $title . '</span>' : '';  
                    $s .= '<li><a class="' . $class . '" title="' . $title . '" href="' . $href . '"><img alt="' . $title . '" src="images/icons/' . $key . $postfix . '.gif" />' . $labelElement . '</a></li>';

                    
                }
                $s .= '</ul></td>';
            }

            
            $s .= '<td class="right"><ul>';
            if(count($modes) > 1) {
                $modes = array_reverse($modes);
                foreach($modes as $mode) {
                    $s .= '<li><a onclick="setGridmodus(\'' . $prefix . '\', \'' . $mode . '\'); return false;" title="' . $mode . '" href="#"><img alt="' . $mode . '" src="images/icons/gridmodus_' . $mode . '_clickable.gif" /></a></li>';    
                }
            }
            $s .= '</ul></td>';            
            $s .= '</tr></table>';
        }
        return $s;
        
    }

    
    public function getTreeHtml($prefix, $a, $inputType = '', $bewerk = false, $level = 0) {

        $page = $this->getPage();

        $class = ($level == 0) ? ' class="tree"' : '';
        $level = $level + 1;
        $s = '<ul' . $class . '>' . PHP_EOL;
        $counter = 0;
        switch($inputType) {
            case 'checkbox' :
                $inputElement = '<input type="checkbox" />';
                break;
            case 'radio' :
                $inputElement = '<input type="radio" name="radio_' . $prefix . '" />';
                break;
            default :
                $inputElement = '';
        }
        foreach($a as $v) {
            $counter++;
            if(!empty($v['_href'])) {
                $label = '<a class="clickable" href="' . $v['_href'] . '">' . $v['_label'] . '</a>';                
            } else {
                $label = '<span>' . $v['_label'] . '</span>';
            }
            if(!empty($v['_items'])) {
                $subitems = PHP_EOL . $this->getTreeHtml($prefix, $v['_items'], $inputType, $bewerk, $level) . PHP_EOL;
            } else {
                $subitems = '';
            }
            if(!empty($v['_icon'])) {
                $icon = '<img src="images/icons/' . $v['_icon'] . '.gif" alt="" />' . PHP_EOL;
            } else {
                $icon = '';
            }
            if(!$bewerk) {
                $bewerkElement = '';
            } else {
                if(!empty($v['_bewerkhref'])) {
                    $bewerkElement = '<a class="action" title="Bewerk" href="' . $v['_bewerkhref'] . '"><img alt="Bewerk" src="timages/icons/bewerk_clickable.gif" /></a>';
                } else {
                    $bewerkElement = '<a class="action" title="Bewerk" href="#"><img alt="Bewerk" src="images/icons/bewerk.gif" /></a>';
                }
            }            
            $id = $prefix . '_' . $level . '-' . $counter;
            $class = isset($_SESSION['tree'][$page][$id]) ? $_SESSION['tree'][$page][$id] : 'collapsed';
            if(!empty($v['_items'])) {
                $src = ($class == 'expanded') ? 'inklappen_clickable.gif' : 'uitklappen_clickable.gif';
                $image = '<img class="toggle" onclick="toggleTree(this); return false;" src="images/icons/' . $src . '" />';    
            } else {
                $image = '<img src="themes/' . $this->theme . '/images/blanco.gif" />';
            }
            $s .= '<li class="' . $class . '" id="' . $id . '">' . $image . $inputElement . $icon . $label . $bewerkElement . $subitems . '</li>' . PHP_EOL;

        }
        $s .= '</ul>' . PHP_EOL;
        
        return $s;

    }
    

    public function getAudittrailHtml() {

        
        /*
        $page = $this->getPage();
        $style = 'none';
        $src = 'uitrollen_clickable.gif';
        if(isset($_SESSION['audittrail'][$page]['1'])) {
            if($_SESSION['audittrail'][$page]['1'] != 'none') { 
                $style = $_SESSION['audittrail'][$page]['1'];
                $src = 'inrollen_clickable.gif';
            }
        }
        */
        // <a href="#" onclick="toggleAudittrail(this); return false;"><img class="icon" src="images/icons/' . $src . '" /></a>
        // </tbody>
        // <tbody>
        // <tbody style="display: ' . $style . ';">
        
        $s = '
    <div class="audittrail">
        <table>
                    <tbody>
                        <tr>
                            <td><img alt="Aangepast" title="Aangepast" src="images/icons/aangepast.gif" /></td>
                            <td>16-05-2009 10:15:35</td>
                            <td>Mark Lindeman</td>
                        </tr>
                        <tr>
                            <td><img alt="Aangepast" title="Aangepast" src="images/icons/aangepast.gif" /></td>
                            <td>15-05-2009 09:01:12</td>
                            <td>Mark Lindeman</td>
                        </tr>
                        <tr>
                            <td><img alt="Aangepast" title="Aangepast" src="images/icons/aangepast.gif" /></td>
                            <td>12-05-2009 15:25:24</td>
                            <td>Lex Biesenbeek</td>
                        </tr>
                        <tr>
                            <td><img alt="Aangepast" title="Aangepast" src="images/icons/aangepast.gif" /></td>
                            <td>12-05-2009 15:24:50</td>
                            <td>Mark Lindeman</td>
                        </tr>
                        <tr>
                            <td><img alt="Aangemaakt" title="Aangemaakt" src="images/icons/aangemaakt.gif" /></td>
                            <td>12-05-2009 15:20:46</td>
                            <td>Lex Biesenbeek</td>
                        </tr>
                    </tbody>
                </table>
                </div>';

        return $s;
        
    }

    
    public function getFormNextActionHtml() {

        $s = '
<tbody>
    <tr>
        <th>Na opslaan</th>    
        <td class="value" colspan="99">
            <select>
                <option>Terug naar overzicht</option>
                <option>Terug naar deze pagina</option>
                <option>Nieuw record</option>
                <option>Naar volgend record</option>
            </select>
        </td>
    </tr>
    <tr>
        <th class="formsectionseparator" colspan="11" />
    </tr>
</tbody>';
        return $s;
        
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    

    

    
    
    
    
    
    
    public function formatInteger($number) {
        
        return number_format($number, 0, ',', '.');
        
    }
    
    


    
    



    
}

?>

