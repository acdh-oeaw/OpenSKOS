<?php

$html = '
<form action="" class="fullwidth">

<table>
    <tbody>
    	<tr>
            <th>Toepassen op</th>    
            <td class="value"><input type="radio" name="range" id="range_selected" checked="checked"><label for="range_selected">Gemarkeerde records (6)</label> <input type="radio" name="range" id="range_all"><label for="range_all">Alle records in resultaatlijst (7.249)</label></td>
        </tr><!--
        <tr>
            <th>Invoermethode</th>    
            <td class="value"><input type="radio" name="methode" id="methode_handmatig" checked="checked"><label for="methode_handmatig">Handmatig</label> <input type="radio" name="methode" id="methode_metadata"><label for="methode_metadata">Op basis van IPTC- en/of EXIF-metadata</label></td>
        </tr>-->
        <tr>
            <th>Gebruik formulier</th>    
            <td class="value">
                <select>
                    <option value="volledig">volledig</option>
                    <option value="vrijwilligers">vrijwilligers</option>
                </select>
            </td>
        </tr>
    </tbody>
    <tbody>
        <tr>
            <th></th>    
            <td>
                <input class="clickable" type="submit" value="Volgende" onclick="loadInModalBox(\'?n1=collectiebeheer&n2=verzamelingen&sub=modalbox_bulkinvoer_02\', \'triple\'); return false;"/>
                <input class="clickable" type="button" value="Annuleer" onclick="closeModalbox();" />
            </td>
        </tr>
    </tbody>
</table>
</form>

';


$content = $html;

$this->setTemplate('modalbox', true);
$this->setTitle('Bulkinvoer (stap 1 van 2)', '');
$this->setContent($content);