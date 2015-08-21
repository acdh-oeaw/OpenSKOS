<?php

$html = '
<form class="fullwidth" style=";">
    <table>
        <tr>
            <td style="padding-right: 10px;">
                <table class="down">
                    <tr>
                        <th>Language</th>
                    </tr>
                    <tr>
			            <td class="value">
			                <!-- <input type="checkbox" checked="checked" name="searchLanguage" id="searchLanguageAll" /><label for="searchLanguageAll">all</label> -->
			                <input type="checkbox" checked="checked" name="searchLanguage" id="searchLanguage1" /><label for="searchLanguage1">EN</label>
			                <input type="checkbox" checked="checked" name="searchLanguage" id="searchLanguage2" /><label for="searchLanguage2">NL</label>
			                <input type="checkbox" checked="checked" name="searchLanguage" id="searchLanguage3" /><label for="searchLanguage3">FR</label>
			            </td>
                    </tr>
                    <tr>
                        <th>Status</th>
                    </tr>
                    <tr>
			            <td class="value">
			                <input type="checkbox" checked="checked" name="searchStatus" id="searchStatusApproved" /><label for="searchStatusApproved">approved</label><br />
			                <input type="checkbox" checked="checked" name="searchStatus" id="searchStatusCandidate" /><label for="searchStatusCandidate">candidate</label><br />
			                <input type="checkbox" checked="checked" name="searchStatus" id="searchStatusExpired" /><label for="searchStatusExpired">expired</label><br />
			                <input type="checkbox" checked="checked" name="searchStatus" id="searchStatusTobechecked" /><label for="searchStatusTobechecked">to be checked</label>
			            </td>
                    </tr>
                    <tr>
                        <th>Only top concepts</th>
                    </tr>
                    <tr>
                        <td class="value"><input type="checkbox" name="onlytopconcepts" /></td>
                    </tr>  
                 </table>
            </td>
            <td>
                <table class="down">
                    <tr>
                        <th>Lexical label</th>
                    </tr>
                    <tr>
                        <td class="value">
                            <input type="checkbox" checked="checked" name="searchLabel" id="searchLabelPreferred" /><label for="searchLabelPreferred">preferred</label><br />
                            <input type="checkbox" checked="checked" name="searchLabel" id="searchLabelAlternative" /><label for="searchLabelAlternative">alternative</label><br />
                            <input type="checkbox" checked="checked" name="searchLabel" id="searchLabelHidden" /><label for="language2">hidden</label>
                        </td>
                    </tr>
                    <tr>
                        <th>Document properties</th>
                    </tr>
                    <tr>
                        <td class="value">
                            <input type="checkbox" checked="checked" name="searchDocumentProperties" id="searchDocumentPropertiesDefinition" /><label for="searchDocumentPropertiesDefinition">definition</label><br />
                            <input type="checkbox" checked="checked" name="searchDocumentProperties" id="searchDocumentPropertiesExample" /><label for="searchDocumentPropertiesExample">example</label><br />
                            <input type="checkbox" checked="checked" name="searchDocumentProperties" id="searchDocumentPropertiesChangeNote" /><label for="searchDocumentPropertiesChangeNote">change note</label><br />
                            <input type="checkbox" checked="checked" name="searchDocumentProperties" id="searchDocumentPropertiesEditorialNote" /><label for="searchDocumentPropertiesEditorialNote">editorial note</label><br />
                            <input type="checkbox" checked="checked" name="searchDocumentProperties" id="searchDocumentPropertiesHistoryNote" /><label for="searchDocumentPropertiesHistoryNote">history note</label><br />
                            <input type="checkbox" checked="checked" name="searchDocumentProperties" id="searchDocumentPropertiesScopeNote" /><label for="searchDocumentPropertiesScopeNote">scope note</label>
                
                        </td>
                    </tr>
                 </table>
            </td>
        </tr>
        <tr>
            <td style="padding-right: 10px;">
                <table class="down">
                    <tr>
                        <th>Created by</th>
                    </tr>
                    <tr>
                        <td class="value">
                            <select multiple="multiple" size="5">
                                <option value="user01" selected="selected">First user</option>
                                <option value="user02" selected="selected">Second user</option>
                                <option value="user03" selected="selected">Third user</option>
                                <option value="user04" selected="selected">Fourth user</option>
                                <option value="user05" selected="selected">Fifth user</option>
                                <option value="user06" selected="selected">Sixth user</option>
                                <option value="user07" selected="selected">Seventh user</option>
                                <option value="user08" selected="selected">Eighth user</option>
                            </select> 
                        </td>
                    </tr>
                    <tr>
                        <td class="value">
                            <table>
                                <tr>
                                    <td>From</td>
                                    <td>
                                        <input type="text" value="" maxlength="2" size="2" style="width: auto;">
                                        <input type="text" value="" maxlength="2" size="2" style="width: auto; margin-left: 2px;">
                                        <input type="text" value="" maxlength="4" size="4" style="width: auto; margin-left: 2px;">
                                    </td>
                                </tr>
                                <tr>
                                    <td>To</td>
                                    <td>
                                        <input type="text" value="" maxlength="2" size="2" style="width: auto;">
                                        <input type="text" value="" maxlength="2" size="2" style="width: auto; margin-left: 2px;">
                                        <input type="text" value="" maxlength="4" size="4" style="width: auto; margin-left: 2px;">
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
            <td>
                <table class="down">
                    <tr>
                        <th>Modified by</th>
                    </tr>
                    <tr>
                        <td class="value">
                            <select multiple="multiple" size="5">
                                <option value="user01" selected="selected">First user</option>
                                <option value="user02" selected="selected">Second user</option>
                                <option value="user03" selected="selected">Third user</option>
                                <option value="user04" selected="selected">Fourth user</option>
                                <option value="user05" selected="selected">Fifth user</option>
                                <option value="user06" selected="selected">Sixth user</option>
                                <option value="user07" selected="selected">Seventh user</option>
                                <option value="user08" selected="selected">Eighth user</option>
                            </select> 
                        </td>
                    </tr>
                    <tr>
                        <td class="value">
                            <table>
                                <tr>
                                    <td>From</td>
                                    <td>
                                        <input type="text" value="" maxlength="2" size="2" style="width: auto;">
                                        <input type="text" value="" maxlength="2" size="2" style="width: auto; margin-left: 2px;">
                                        <input type="text" value="" maxlength="4" size="4" style="width: auto; margin-left: 2px;">
                                    </td>
                                </tr>
                                <tr>
                                    <td>To</td>
                                    <td>
                                        <input type="text" value="" maxlength="2" size="2" style="width: auto;">
                                        <input type="text" value="" maxlength="2" size="2" style="width: auto; margin-left: 2px;">
                                        <input type="text" value="" maxlength="4" size="4" style="width: auto; margin-left: 2px;">
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input class="clickable" type="button" value="Ok" onclick="closeModalbox();"/>
                <input class="clickable" type="button" value="Cancel" onclick="closeModalbox();" />
            </td>
        </tr>
    </table>

</form>
';


$content = $html;

//$content = 'asdlaishdasodhiasodhi';

$this->setTemplate('modalbox', 'a', true);
$this->setTitle('Advanced search options', '');
$this->setContent($content);