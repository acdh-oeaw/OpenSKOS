<?php

// ================
// Some basic stuff
// ================

// Grid 'concepts'
$gridColumns = array(
    'conceptscheme' => array('label' => 'Scheme', 'default' => '<img alt="X1" title="X1" src="images/icons/conceptscheme_01.gif" />', 'style' => 'width: 1px; white-space: nowrap;'),
    'concept' => array('label' => 'Preferred label', 'default' => 'Label of the concept', 'style' => ''),
);

$gridData = array(
    array('<img alt="X1" title="X1" src="images/icons/conceptscheme_01.gif" />', 'First concept'),
    array('<img alt="X1" title="X1" src="images/icons/conceptscheme_01.gif" /><img alt="Y2" title="Y2" src="images/icons/conceptscheme_02.gif" />', 'Second concept'),
    array('<img alt="Y2" title="Y2" src="images/icons/conceptscheme_02.gif" />', 'Third concept'),
    array('<img alt="X1" title="X1" src="images/icons/conceptscheme_01.gif" />', 'Fourth concept'),
    array('<img alt="X1" title="X1" src="images/icons/conceptscheme_01.gif" /><img alt="Y2" title="Y2" src="images/icons/conceptscheme_02.gif" /><img alt="Z3" title="Z3" src="images/icons/conceptscheme_03.gif" />', 'Fifth concept'),
    array('<img alt="X1" title="X1" src="images/icons/conceptscheme_01.gif" /><img alt="Y2" title="Y2" src="images/icons/conceptscheme_02.gif" /><img alt="Z3" title="Z3" src="images/icons/conceptscheme_03.gif" />', 'Sixth concept'),
    array('<img alt="Z3" title="Z3" src="images/icons/conceptscheme_03.gif" />', 'Seventh concept'),
    array('<img alt="Y2" title="Y2" src="images/icons/conceptscheme_02.gif" />', 'Eighth concept'),
    array('<img alt="X1" title="X1" src="images/icons/conceptscheme_01.gif" /><img alt="Z3" title="Z3" src="images/icons/conceptscheme_03.gif" />', 'Ninth concept'),
    array('<img alt="X1" title="X1" src="images/icons/conceptscheme_01.gif" />', 'Tenth concept'),
    array('<img alt="X1" title="X1" src="images/icons/conceptscheme_01.gif" />', 'Eleventh concept')
);

$gridOptions = array(
    'rowstotal' => 100,    
    'rowsperpage' => 50,
    'showcheckboxes' => false,
    'shownumbers' => false,
    'showpager' => false,
    'modes' => array('lijst'),
    // 'thumbkey' => 'xxxxxxxxx',
    'showfilters' => false,
    'showhead' => false,
    'rowaction' => array('label' => 'Open concept', 'onclick' => 'showConceptDetail();'),
);


// =====================
// Panel 1 (search form)
// =====================

$actions = array(
    'advancedoptions' => array('label' => 'Show advanced search options', 'href' => 'javascript:openModalbox(\'?n1=management&n2=search&sub=modalbox_advancedoptions\', \'single\');'),
);
$actionHtml = $this->getActionHtml($actions, array(), '');

$content1 = '
<style type="text/css">

    DIV#content DIV#panel_1 {
        background: #dddddd; 
    }

    DIV#content DIV#panel_1 DIV.panel_inner {
        padding: 0;
    }
    
    DIV#content DIV#panel_1 FORM {
        background: none;
    }

</style>
<script language="JavaScript" type="text/javascript">

	window.addEvent(\'domready\', function() {
	
	    $(\'term\').select();

	    
	    $(\'term\').addEvent(\'keyup\', function() {
            if($(\'instant\').checked) {
                if($(\'term\').value.length == 0) {
                    hideResults();
                } else {
	                showResults();
                }
            }
	    });

	    
	    $(\'searchform\').addEvent(\'submit\', function(e) {
            new Event(e).stop();
            if($(\'term\').value.length == 0) {
                hideResults();
            } else {
                showResults();
            }
            $(\'term\').focus();
	    });

	    
	    $(\'instant\').addEvent(\'click\', function() {
            if(this.checked) {
	            $(\'search\').setAttribute(\'disabled\', \'disabled\');
	            $(\'search\').removeClass(\'clickable\');
                // $(\'truncateLeft\').removeProperty(\'checked\');
                // $(\'truncateBoth\').removeProperty(\'checked\');
                // $(\'truncateRight\').setProperty(\'checked\', \'checked\');
                // $(\'truncateRight\').setAttribute(\'disabled\', \'disabled\');
                // $(\'truncateLeft\').setAttribute(\'disabled\', \'disabled\');
                // $(\'truncateBoth\').setAttribute(\'disabled\', \'disabled\');
                // $(\'truncateRight\').disabled = true;
                // $(\'truncateLeft\').disabled = true;
                // $(\'truncateBoth\').disabled = true;
            } else {
                $(\'search\').removeAttribute(\'disabled\');
                $(\'search\').addClass(\'clickable\');
	            // $(\'truncateRight\').removeProperty(\'disabled\');
	            // $(\'truncateLeft\').removeProperty(\'disabled\');
	            // $(\'truncateBoth\').removeProperty(\'disabled\');
            }
            $(\'term\').select();
        });
	    
	});
	
	
	function showConceptDetail() {
	
        $(\'concept_message\').setStyle(\'display\', \'none\');    
        $(\'concept_edit\').setStyle(\'display\', \'none\');    
        $(\'concept_view\').setStyle(\'display\', \'block\');

	}
		
	
    function switchToEditMode() {
    
        $(\'concept_view\').setStyle(\'display\', \'none\');
        $(\'concept_edit\').setStyle(\'display\', \'block\');    
    }
    
    
    function switchToViewMode() {
    
        $(\'concept_edit\').setStyle(\'display\', \'none\');    
        $(\'concept_view\').setStyle(\'display\', \'block\');

    }
    
    
    function showResults() {
    
	    var rowsToShow = 24 - ($(\'term\').value.length * 4);
        if(rowsToShow < 0) {
            rowsToShow = 0;
        }
        if(rowsToShow == 0) {
            $(\'search_result_message\').setStyle(\'display\', \'none\');
            $(\'search_result\').setStyle(\'display\', \'none\');
            $(\'search_result_empty\').setStyle(\'display\', \'block\');
            $(\'term_not_found\').getFirst().setText($(\'term\').value);
        } else {
	        $(\'search_result\').getElements(\'.gridtable tr\').each(function(trElement, index){
	            // console.log(index);
	            // console.log(trElement);
	            var newStyle = (index <= rowsToShow) ? \'\' : \'none\';
	            trElement.setStyle(\'display\', newStyle);
	        });
	        $(\'search_result_message\').setStyle(\'display\', \'none\');
	        $(\'search_result_empty\').setStyle(\'display\', \'none\');
	        $(\'search_result\').setStyle(\'display\', \'block\');
        }

    }
    

    function hideResults() {
    
	    $(\'search_result\').setStyle(\'display\', \'none\');
	    $(\'search_result_empty\').setStyle(\'display\', \'none\');    
	    $(\'search_result_message\').setStyle(\'display\', \'\');
    
    }

    
</script>
<form id="searchform" class="fullwidth" style="margin-bottom: 0;">
	<table>
        <tr>
            <td class="value" colspan="2" style="padding-bottom: 20px;"><input name="term" id="term" type="text" autocomplete="off" /></td>
        </tr>
        <tr>
            <th>Truncate</th>
            <td class="value">
                <input type="radio" name="truncate" id="truncateRight" checked="checked" /><label for="truncateRight">right</label>
                <input type="radio" name="truncate" id="truncateLeft" /><label for="truncateLeft">left</label>
                <input type="radio" name="truncate" id="truncateBoth" /><label for="truncateBoth">both</label>
            </td>
        </tr>
        <tr>
            <th>Concept scheme</th>    
            <td class="value">
				<select name="searchConceptScheme" id="searchConceptScheme">
				    <option value="user01" selected="selected">- all -</option>    
				    <option value="user01">X1</option>
				    <option value="user02">Y2</option>
				    <option value="user03">Z3</option>
				</select>
            </td>
        </tr>
        <tr>
            <th><label for="instant">Instant results</label></th>
            <td class="value"><input type="checkbox" name="instant" id="instant" checked="checked" /></td>
        </tr>
        <tr>
            <td colspan="2">' . $actionHtml . '</td>
        </tr>
        <tr>
            <td colspan="2"><input type="submit" id="search" value="Search" disabled="disabled" style="margin: 0; width: 100%;" /></td>
        </tr>
    </table>
</form>';


// =======================
// Panel 2 (search result)
// =======================

$gridRowActions = array(
    'voegtoeaanselectie'    => array('label' => 'Add to selection', 'href' => 'javascript:alert(\'[ACTION]\n\nAdd this concept to the selection grid on the right.\');'),
    'relaties'              => array('label' => 'Relate to concept', 'href' => 'javascript:openModalbox(\'?n1=management&n2=search&sub=modalbox_addrelation_01\', \'single\');')
);

$gridActions = array(
    'voegtoeaanselectie' => array('label' => 'Add to selection', 'href' => ''),    
    'relaties' => array('label' => 'Relate to concept', 'href' => ''),
    // 'bulkinvoer' => array('label' => 'Data entry', 'href' => '')
);

$actions = array(
    'nieuw' => array('label' => 'Add this term', 'href' => 'javascript:openModalbox(\'?n1=management&n2=search&sub=modalbox_addterm_01&term=\' + $(\'term\').value, \'single\');')
);
$actionHtml = $this->getActionHtml($actions);


$content2 = '
<div id="search_result_message"><h2>Search result</h2><p>No search performed.</p></div>
<div id="search_result" style="display: none;"><h2>Search result</h2>' . $this->getGridHtml('search_result_grid', $gridColumns, $gridRowActions, $gridData, $gridOptions, $gridActions) . '</div>
<div id="search_result_empty" style="display: none;"><h2>Search result</h2><p>No results found for <span id="term_not_found"><strong></strong></span>.</p>' . $actionHtml . '</div>';



// ========================
// Panel 3 (concept detail)
// ========================

// VIEW MODE
$paneltabViewLanguage1 = '
<form class="fullwidth">
    <table>
        <tr>
            <th class="formsection" colspan="10"><h2>Lexical labels</h2></th>
        </tr>
        <tr>
            <th>Preferred label</th>
            <td class="value" colspan="9">First concept</td>
        </tr>
        <tr>
            <th>Alternative label</th>
            <td class="value" colspan="9">1st concept</td>
        </tr>
        <tr>
            <th colspan="11" class="formsectionseparator"></th>
        </tr>
        <tr>
            <th class="formsection" colspan="10"><h2>Documentation properties</h2></th>
        </tr>
        <tr>
            <th>Definition</th>
            <td class="value" colspan="9">Nullam id massa nunc. Vivamus mi nunc, fringilla ut euismod vel, ultrices ac nisi. Nullam faucibus pretium enim, vitae mattis felis ullamcorper a. Proin sed lectus nibh. Nullam placerat viverra iaculis. Quisque eu purus id odio vestibulum euismod quis ac eros.</td>
        </tr>
        <tr>
            <th>Change note</th>
            <td class="value" colspan="9">Etiam placerat tempus eros, non suscipit tortor scelerisque tincidunt. Donec vitae molestie elit. Praesent consectetur, lorem nec semper laoreet, libero tellus malesuada lorem, nec eleifend tortor orci eget arcu.</td>
        </tr>
        <tr>
            <th>Scope note</th>
            <td class="value" colspan="9">Aliquam purus nisi, fermentum nec consectetur tempus, placerat vitae ligula. Morbi ac magna ut dui porttitor mattis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed at erat dui, et viverra leo. Integer luctus aliquet urna, vel porta risus auctor feugiat. Duis in mauris justo. Proin in sapien id tortor dapibus pharetra placerat vitae enim. Suspendisse potenti. Curabitur blandit pretium tincidunt.</td>
        </tr>
    </table>
</form>';

$paneltabViewLanguage2 = $paneltabViewLanguage1;
$paneltabViewLanguage3 = $paneltabViewLanguage2;

$paneltabViewScheme1 = '
<form class="fullwidth">
    <table style="width: 100%;">
        <tr>
            <th>URI</th>
            <td class="value" colspan="9">http://test/12345.html</td>
        </tr>
        <tr>
            <th>Notation</th>
            <td class="value" colspan="9">Donec euismod fringilla</td>
        </tr>
        <tr>
            <th>Is top concept</th>
            <td class="value" colspan="9">yes</td>
        </tr>
    </table>
</form>';


$gridRowActions = array(
);

$gridActions = array(
);

$paneltabViewScheme1 .= '
<h2>Semantic relations</h2>';
$gridOptions['rowstotal'] = 2;
$paneltabViewScheme1 .= '
<h3>Has broader (2)</h3>
' . $this->getGridHtml('relations', $gridColumns, $gridRowActions, $gridData, $gridOptions, $gridActions);

$gridOptions['rowstotal'] = 28;
$paneltabViewScheme1 .= '
<h3>Has narrower (28)</h3>
' . $this->getGridHtml('relations', $gridColumns, $gridRowActions, $gridData, $gridOptions, $gridActions);

$gridOptions['rowstotal'] = 1;
$paneltabViewScheme1 .= '
<h3>Has related (1)</h3>
' . $this->getGridHtml('relations', $gridColumns, $gridRowActions, $gridData, $gridOptions, $gridActions);

$paneltabViewScheme1 .= '
<h2>Mapping properties</h2>';

$gridOptions['rowstotal'] = 2;
$paneltabViewScheme1 .= '
<h3>Has exact match (2)</h3>
' . $this->getGridHtml('relations', $gridColumns, $gridRowActions, $gridData, $gridOptions, $gridActions);

$gridOptions['rowstotal'] = 3;
$paneltabViewScheme1 .= '
<h3>Has close match (3)</h3>
' . $this->getGridHtml('relations', $gridColumns, $gridRowActions, $gridData, $gridOptions, $gridActions);

$gridOptions['rowstotal'] = 3;
$paneltabViewScheme1 .= '
<h3>Has broader match (3)</h3>
' . $this->getGridHtml('relations', $gridColumns, $gridRowActions, $gridData, $gridOptions, $gridActions);

/*
$gridOptions['rowstotal'] = 0;
$paneltabViewScheme1 .= '
<h3>Has narrower match (0)</h3>
' . $this->getGridHtml('relations', $gridColumns, $gridRowActions, $gridData, $gridOptions, $gridActions);

$gridOptions['rowstotal'] = 0;
$paneltabViewScheme1 .= '
<h3>Has related match (0)</h3>
' . $this->getGridHtml('relations', $gridColumns, $gridRowActions, $gridData, $gridOptions, $gridActions);
*/

$paneltabViewScheme2 = $paneltabViewScheme1;
$paneltabViewScheme3 = $paneltabViewScheme2;

$paneltabsViewLanguage = array(
    'view_languages_en' => array('label' => 'EN', 'content' => $paneltabViewLanguage1, 'icon' => 'flag_en'),
    'view_languages_nl' => array('label' => 'NL', 'content' => $paneltabViewLanguage2, 'icon' => 'flag_nl'),
    'view_languages_fr' => array('label' => 'FR', 'content' => $paneltabViewLanguage3, 'icon' => 'flag_fr')
);

$paneltabsViewScheme = array(
    'view_schemes_01' => array('label' => 'X1', 'content' => $paneltabViewScheme1, 'icon' => 'conceptscheme_01'),
    'view_schemes_02' => array('label' => 'Y2', 'content' => $paneltabViewScheme2, 'icon' => 'conceptscheme_02'),
    'view_schemes_03' => array('label' => 'Z3', 'content' => $paneltabViewScheme3, 'icon' => 'conceptscheme_03')
);




// EDIT MODE
$paneltabEditLanguage1 = '
<form class="fullwidth">
    <table>
        <tr>
            <th class="formsection" colspan="10"><h2>Lexical labels</h2></th>
        </tr>
        <tr>
            <th>Preferred label</th>
            <td class="value" colspan="9"><input type="text" name="prefLabel" value="First concept" /></td>
        </tr>
        <tr>
            <th>Alternative label</th>
            <td class="value" colspan="7"><input type="text" name="altLabel[]" value="1st concept" /></td>
            <td class="icon"><img alt="Verwijder" src="images/icons/verwijder.gif"></td>
            <td class="icon"><img alt="Nieuw" src="images/icons/nieuw.gif"></td>
        </tr>
        <tr>
            <th>Hidden label</th>
            <td class="value" colspan="7"><input type="text" name="hiddenLabel[]" /></td>
            <td class="icon"><img alt="Verwijder" src="images/icons/verwijder.gif"></td>
            <td class="icon"><img alt="Nieuw" src="images/icons/nieuw.gif"></td>
        </tr>
        <tr>
            <th colspan="11" class="formsectionseparator"></th>
        </tr>
        <tr>
            <th class="formsection" colspan="10"><h2>Documentation properties</h2></th>
        </tr>
        <tr>
            <th>Definition</th>
            <td class="value" colspan="7"><textarea name="definition">Nullam id massa nunc. Vivamus mi nunc, fringilla ut euismod vel, ultrices ac nisi. Nullam faucibus pretium enim, vitae mattis felis ullamcorper a. Proin sed lectus nibh. Nullam placerat viverra iaculis. Quisque eu purus id odio vestibulum euismod quis ac eros.</textarea></td>
            <td class="icon"><img alt="Verwijder" src="images/icons/verwijder.gif"></td>
            <td class="icon"><img alt="Nieuw" src="images/icons/nieuw.gif"></td>
        </tr>
        <tr>
            <th>Change note</th>
            <td class="value" colspan="7"><textarea name="changeNote">Etiam placerat tempus eros, non suscipit tortor scelerisque tincidunt. Donec vitae molestie elit. Praesent consectetur, lorem nec semper laoreet, libero tellus malesuada lorem, nec eleifend tortor orci eget arcu.</textarea></td>
            <td class="icon"><img alt="Verwijder" src="images/icons/verwijder.gif"></td>
            <td class="icon"><img alt="Nieuw" src="images/icons/nieuw.gif"></td>
        </tr>
        <tr>
            <th>Scope note</th>
            <td class="value" colspan="7"><textarea name="scopeNote">Aliquam purus nisi, fermentum nec consectetur tempus, placerat vitae ligula. Morbi ac magna ut dui porttitor mattis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed at erat dui, et viverra leo. Integer luctus aliquet urna, vel porta risus auctor feugiat. Duis in mauris justo. Proin in sapien id tortor dapibus pharetra placerat vitae enim. Suspendisse potenti. Curabitur blandit pretium tincidunt.</textarea></td>
            <td class="icon"><img alt="Verwijder" src="images/icons/verwijder.gif"></td>
            <td class="icon"><img alt="Nieuw" src="images/icons/nieuw.gif"></td>
        </tr>
        <tr>
            <th colspan="11" class="formsectionseparator"></th>
        </tr>
        <tr>
            <th></th>
            <td class="value" colspan="9">
                <select name="documentationPropertyType">
	                <!-- <option value="Definition" selected="selected">Definition</option> -->
	                <option value="Example">Example</option>
	                <!-- <option value="Change note">Change note</option> -->
	                <option value="Editorial note">Editorial note</option>
	                <option value="History note">History note</option>
	                <!-- <option value="Scope note">Scope note</option> -->
                </select>
            </td>
        </tr>
        <tr>
            <th></th>
            <td class="value" colspan="9"><textarea name="newDocumentationProperty"></textarea></td>
        </tr>
        <tr>
            <th></th>
            <td><input type="button" value="Add documentation property" /></td>
        </tr>
    </table>
</form>';

$paneltabEditLanguage2 = $paneltabEditLanguage1;
$paneltabEditLanguage3 = $paneltabEditLanguage2;

$paneltabEditScheme1 = '
<form class="fullwidth">
    <table style="width: 100%;">
        <tr>
            <th>URI</th>
            <td class="value" colspan="9"><input type="text" name="uri" value="http://test/12345.html" /></td>
        </tr>
        <tr>
            <th>Notation</th>
            <td class="value" colspan="9"><input type="text" name="notation" value="Donec euismod fringilla" /></td>
        </tr>
        <tr>
            <th><label for="istopconcept">Is top concept</label></th>
            <td class="value" colspan="9"><input type="checkbox" name="istopconcept" id="istopconcept" checked="checked" /></td>
        </tr>
    </table>
</form>';


$gridRowActions = array(
    'verwijder'             => array('label' => 'Remove relation', 'href' => ''),
);

$gridActions = array(
);

$paneltabEditScheme1 .= '
<h2>Semantic relations</h2>';
$gridOptions['rowstotal'] = 2;
$paneltabEditScheme1 .= '
<h3>Has broader (2)</h3>
' . $this->getGridHtml('relations', $gridColumns, $gridRowActions, $gridData, $gridOptions, $gridActions);

$gridOptions['rowstotal'] = 28;
$paneltabEditScheme1 .= '
<h3>Has narrower (28)</h3>
' . $this->getGridHtml('relations', $gridColumns, $gridRowActions, $gridData, $gridOptions, $gridActions);

$gridOptions['rowstotal'] = 1;
$paneltabEditScheme1 .= '
<h3>Has related (1)</h3>
' . $this->getGridHtml('relations', $gridColumns, $gridRowActions, $gridData, $gridOptions, $gridActions);

$paneltabEditScheme1 .= '
<h2>Mapping properties</h2>';

$gridOptions['rowstotal'] = 2;
$paneltabEditScheme1 .= '
<h3>Has exact match (2)</h3>
' . $this->getGridHtml('relations', $gridColumns, $gridRowActions, $gridData, $gridOptions, $gridActions);

$gridOptions['rowstotal'] = 3;
$paneltabEditScheme1 .= '
<h3>Has close match (3)</h3>
' . $this->getGridHtml('relations', $gridColumns, $gridRowActions, $gridData, $gridOptions, $gridActions);

$gridOptions['rowstotal'] = 3;
$paneltabEditScheme1 .= '
<h3>Has broader match (3)</h3>
' . $this->getGridHtml('relations', $gridColumns, $gridRowActions, $gridData, $gridOptions, $gridActions);

/*
$gridOptions['rowstotal'] = 0;
$paneltabEditScheme1 .= '
<h3>Has narrower match (0)</h3>
' . $this->getGridHtml('relations', $gridColumns, $gridRowActions, $gridData, $gridOptions, $gridActions);

$gridOptions['rowstotal'] = 0;
$paneltabEditScheme1 .= '
<h3>Has related match (0)</h3>
' . $this->getGridHtml('relations', $gridColumns, $gridRowActions, $gridData, $gridOptions, $gridActions);
*/

$paneltabEditScheme2 = $paneltabEditScheme1;
$paneltabEditScheme3 = $paneltabEditScheme2;

$paneltabsEditLanguage = array(
    'edit_languages_en' => array('label' => 'EN', 'content' => $paneltabEditLanguage1, 'icon' => 'flag_en'),
    'edit_languages_nl' => array('label' => 'NL', 'content' => $paneltabEditLanguage2, 'icon' => 'flag_nl'),
    'edit_languages_fr' => array('label' => 'FR', 'content' => $paneltabEditLanguage3, 'icon' => 'flag_fr')
);

$paneltabsEditScheme = array(
    'edit_schemes_01' => array('label' => 'X1', 'content' => $paneltabEditScheme1, 'icon' => 'conceptscheme_01'),
    'edit_schemes_02' => array('label' => 'Y2', 'content' => $paneltabEditScheme2, 'icon' => 'conceptscheme_02'),
    'edit_schemes_03' => array('label' => 'Z3', 'content' => $paneltabEditScheme3, 'icon' => 'conceptscheme_03')
);

$content3 = '

    <div id="concept_message"><h2>Concept detail</h2><p>No concept chosen.</p></div>
    <div id="concept_view" style="display: none;">
        <h2>First concept</h2>
        <form class="fullwidth">
            <table>
                <tr>
                    <td>
                        <table>
                            <tr>
                                <th>Status</th>
                                <td class="value">candidate</td>
                            </tr>
                            <tr>
                                <th>To be checked</th>
                                <td class="value">no</td>
                            </tr>
                        </table>
                    </td>
                    <td style="text-align: right;">
                        <input type="button" value="Switch to edit mode" class="clickable" onclick="switchToEditMode();" />
                    </td>
                </tr>
            </table>
        </form>
        <table style="width: 100%;">
           <tr>
               <td style="width: 50%;">' . $this->getPaneltabHtml('panelTabsViewLanguage', $paneltabsViewLanguage, false) . '</td>
               <td style="padding-left: 15px;">' . $this->getPaneltabHtml('paneltabsViewScheme', $paneltabsViewScheme, false) . '</td>
           </tr>
        </table>
        <form class="fullwidth">
            <table>
                <tr>
                    <th>Created</th>
                    <th>Last changed</th>
                    <th>Approved</th>
                </tr>
                <tr>
                    <td>12-06-2011 11:03:16</td>
                    <td>14-06-2011 09:08:03</td>
                    <td>19-06-2011 15:22:19</td>
                </tr>
                <tr>
                    <td>First user</td>
                    <td>Second user</td>
                    <td>Third user</td>
                </tr>
            </table>
        </form>
    </div>
    <div id="concept_edit" style="display: none;">
        <h2>First concept</h2>
        <form class="fullwidth">
            <table>
                <tr>
                    <td>
                        <table>
                            <tr>
                                <th>Status</th>
                                <td class="value" nowrap="nowrap">
                                    <input type="radio" name="status" id="status_candidate" checked="checked" /><label for="status_candidate">candidate</label>
                                    <input type="radio" name="status" id="status_approved" /><label for="status_approved">approved</label>
                                    <input type="radio" name="status" id="status_expired" /><label for="status_expired">expired</label>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="tobechecked">To be checked</label></th>
                                <td class="value"><input type="checkbox" name="tobechecked" id="tobechecked" /></td>
                            </tr>
                        </table>
                    </td>
                    <td style="text-align: right;" nowrap="nowrap">
                        <input type="button" value="Save" />
                        <input type="button" value="Switch to view mode" class="clickable" onclick="switchToViewMode();" />
                    </td>
                </tr>
            </table>
        </form>
        <table style="width: 100%;">
           <tr>
               <td style="width: 50%;">' . $this->getPaneltabHtml('panelTabsEditLanguage', $paneltabsEditLanguage, true) . '</td>
               <td style="padding-left: 15px;">' . $this->getPaneltabHtml('paneltabsEditScheme', $paneltabsEditScheme, true) . '</td>
           </tr>
        </table>
        <form class="fullwidth">
            <table>
                <tr>
                    <th>Created</th>
                    <th>Last changed</th>
                    <th>Approved</th>
                </tr>
                <tr>
                    <td>12-06-2011 11:03:16</td>
                    <td>14-06-2011 09:08:03</td>
                    <td>19-06-2011 15:22:19</td>
                </tr>
                <tr>
                    <td>First user</td>
                    <td>Second user</td>
                    <td>Third user</td>
                </tr>
            </table>
        </form>
    </div>
    
    
';


// ===============================
// Panel 4 (history and selection)
// ===============================

// History
$gridRowActions = array(
//    'relaties'              => array('label' => 'Relate to concept', 'href' => 'javascript:openModalbox(\'?n1=management&n2=search&sub=modalbox_addrelation_01\', \'single\');')
);

$gridActions = array(
    'verwijder' => array('label' => 'Clear history', 'href' => '')
);

$gridOptions['rowstotal'] = 24;

$paneltabHistory = $this->getGridHtml('history', $gridColumns, $gridRowActions, $gridData, $gridOptions, $gridActions);

// Selection
$gridRowActions = array(
    'verwijderuitselectie'  => array('label' => 'Remove from selection', 'href' => 'javascript:alert(\'[ACTION]\n\nRemove from this grid.\');'),
    'relaties'              => array('label' => 'Relate to concept', 'href' => 'javascript:openModalbox(\'?n1=management&n2=search&sub=modalbox_addrelation_01\', \'single\');')
);

$gridActions = array(
    'verwijder' => array('label' => 'Clear selection', 'href' => ''),	
	'relaties' => array('label' => 'Relate to concept', 'href' => ''),
    //'bulkinvoer' => array('label' => 'Data entry', 'href' => '')
);

$gridOptions['rowstotal'] = 16;

$paneltabSelection = $this->getGridHtml('selection', $gridColumns, $gridRowActions, $gridData, $gridOptions, $gridActions);




$paneltabsRight = array(
    '01' => array('label' => 'History (24)', 'content' => $paneltabHistory, 'icon' => 'historie'),
    '02' => array('label' => 'Selection (16)', 'content' => $paneltabSelection, 'icon' => 'selectie')
);

$content4 = $this->getPaneltabHtml('panelTabsRight', $paneltabsRight, false);



$this->setTemplate('main', 'e', false);
$this->setTitle('Search, browse and edit');
$this->setContent($content1, $content2, $content3, $content4);