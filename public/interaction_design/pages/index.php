<?php

$content1 = '
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam venenatis, nisl pellentesque sodales volutpat, magna lectus sollicitudin augue, sit amet vulputate nisi mi ac nisl. Maecenas ipsum ante, luctus tempor, luctus et, aliquet et, lectus. Sed cursus est vitae quam.</p>
<p>Phasellus velit urna, pharetra eget, elementum dictum, luctus feugiat, risus. Fusce quis nibh. Vestibulum venenatis imperdiet dolor. Nulla a dui et eros sollicitudin mollis. Donec sit amet ante nec lectus cursus fringilla. In euismod. Donec id mauris nec elit euismod aliquam.</p>
'; 

$content2 = '
<ul class="shortcuts">
    <li>
        <a class="clickable" href="?n1=management">Management</a>
    </li>
    <li>
        <a href="#">What is OpenSKOS?</a>
    </li>
    <li>
        <a href="#">Dashboard</a>
    </li>
    <li>
        <a href="#">API</a>
    </li>
    <li>
        <a href="#">OAI-PMH</a>
    </li>
</ul>
';

$this->setTemplate('main', 'b', false);
$this->setTitle('Welcome to OpenSKOS');
$this->setContent($content1, $content2);