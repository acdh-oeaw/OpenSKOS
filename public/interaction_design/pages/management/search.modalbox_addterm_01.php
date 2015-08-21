<?php

$term = $_GET['term'];

$html = '
<form class="fullwidth" onsubmit="hideResults(); $(\'term\').value = \'\'; $(\'term\').select(); closeModalbox(); showMessage(\'Concept created\', \'<p>Concept <strong>' . $term . '</strong> with status <strong>candidate</strong> has been created.</p>\'); return false;">
    <table>
        <tr>
            <th>Status</th>
            <td>candidate</td>
        </tr>    
        <tr>
            <th>Language</th>
            <td class="value">
                <input type="radio" checked="checked" name="searchLanguage" id="searchLanguage1" /><label for="searchLanguage1">EN</label>
                <input type="radio" name="searchLanguage" id="searchLanguage2" /><label for="searchLanguage2">NL</label>
                <input type="radio" name="searchLanguage" id="searchLanguage3" /><label for="searchLanguage3">FR</label>
            </td>
        </tr>
        <tr>
            <th>Concept scheme</th>    
            <td class="value">
                <select name="searchConceptScheme" id="searchConceptScheme">
                    <option value="user01" selected="selected">X1</option>
                    <option value="user02">Y2</option>
                    <option value="user03">Z3</option>
                </select>
            </td>
        </tr>
        <tr>
            <th>Preferred label</th>
            <td class="value" colspan="9"><input type="text" name="prefLabel" value="' . $term . '" /></td>
        </tr>
        <tr>
            <th>Scope note</th>
            <td class="value" colspan="9"><textarea name="scopeNote"></textarea></td>
        </tr>
        <tr>
            <th></th>
            <td>
                <input class="clickable" type="submit" value="Ok" />
                <input class="clickable" type="button" value="Cancel" onclick="closeModalbox();" />
            </td>
        </tr>
    </table>
</form>
';

$content = $html;

$this->setTemplate('modalbox', 'a', true);
$this->setTitle('Create concept %s', $term);
$this->setContent($content);