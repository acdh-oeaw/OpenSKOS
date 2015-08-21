<?php

$html = '
<h2>Semantic relations</h2>
<ul>
    <li><a href="javascript:alert(\'[ACTION]\n\nAdd this relation to the concept in the middle.\'); closeModalbox();" class="clickable">Has broader</a></li>
    <li><a href="javascript:alert(\'[ACTION]\n\nAdd this relation to the concept in the middle.\'); closeModalbox();" class="clickable">Has narrower</a></li>
    <li><a href="javascript:alert(\'[ACTION]\n\nAdd this relation to the concept in the middle.\'); closeModalbox();" class="clickable">Has related</a></li>
</ul>
<h2>Mapping properties</h2>
<ul>
    <li><a href="javascript:alert(\'[ACTION]\n\nAdd this relation to the concept in the middle.\'); closeModalbox();" class="clickable">Has exact match</a></li>
    <li><a href="javascript:alert(\'[ACTION]\n\nAdd this relation to the concept in the middle.\'); closeModalbox();" class="clickable">Has close match</a></li>
    <li><a href="javascript:alert(\'[ACTION]\n\nAdd this relation to the concept in the middle.\'); closeModalbox();" class="clickable">Has broader match</a></li>
    <li><a href="javascript:alert(\'[ACTION]\n\nAdd this relation to the concept in the middle.\'); closeModalbox();" class="clickable">Has narrower match</a></li>
    <li><a href="javascript:alert(\'[ACTION]\n\nAdd this relation to the concept in the middle.\'); closeModalbox();" class="clickable">Has related match</a></li>
</ul>
';

$content = $html;

$this->setTemplate('modalbox', 'a', true);
$this->setTitle('Relate to current concept');
$this->setContent($content);