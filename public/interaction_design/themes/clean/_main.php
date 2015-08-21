<div id="wrapper">
    <div id="header">
<?php


// ==========
// Breadcrumb
// ==========

//print '        <ul id="breadcrumb">' . PHP_EOL;
//$total = count($this->breadcrumb);
//$i = 0;
//foreach($this->breadcrumb as $a) {
//    if($a[2]) $a[0] = '<strong>' . $a[0] . '</strong>';
//    if (empty($a[1])) {
//        print '            <li><span>' . $a[0] . '</span></li>' . PHP_EOL;
//    } else {
//        print '            <li><a class="clickable" href="' . $a[1] . '">' . $a[0] . '</a></li>' . PHP_EOL;
//    }
//    $i++;
//}
//print '        </ul>' . PHP_EOL; 


// ==========
// Login data
// ==========

if($this->n1 == 'algemeen' && $this->n2 == 'aanmelden') {
    print '        <div id="login_data">U bent niet aangemeld</div>' . PHP_EOL;
} else {
    print '        <div id="login_data">Aangemeld als <strong>Pictura</strong>&nbsp;&nbsp;<a href="?n1=algemeen&amp;n2=aanmelden"><img style="vertical-align: middle;" src="images/icons/logoff.gif" alt="" /></a> <a href="#" onclick="showModalbox(\'?n1=collectiebeheer&n2=verzamelingen&sub=modalbox_help\');" title=""><img style="vertical-align: middle;" src="images/icons/help.gif" alt="" /></a></div>' . PHP_EOL;
}


// ====
// Tabs
// ====

if($this->tabs) {
    print '        <ul id="tabs">' . PHP_EOL;
    foreach($this->tabs as $tab) {
        $class = $tab['current'] ? ' huidig' : '';
        print '            <li class="' . $class . '"><a class="clickable" href="' . $tab['href'] . '">' . $tab['label'] . '</a></li>' . PHP_EOL;
    }
    print '        </ul>' . PHP_EOL;
}


// =======
// Subtabs
// =======

if($this->subtabs) {
    print '        <ul id="subtabs">' . PHP_EOL;
    foreach($this->subtabs as $subtab) {
        $class = $subtab['current'] ? ' huidig' : '';
        print '            <li class="' . $class . '"><a class="clickable" href="' . $subtab['href'] . '">' . $subtab['label'] . '</a></li>' . PHP_EOL;
    }
    print '        </ul>' . PHP_EOL;
}

?>
        <h1><?php

    if(!empty($this->titleValue)) {
        print sprintf($this->titleBase, '<span>' . $this->titleValue . '</span>');
    } else {
        print $this->titleBase;
    }

?></h1>
<?php

    if($this->getLayout() == 'layout_c') {

?>
	    <div id="layoutswitch">
	       <a class="clickable" href="#" onclick="toggleFullmode()">Toggle invoermodus</a>&nbsp;&nbsp;&nbsp;
	       Kies layout: <a class="clickable" href="#" onclick="setLayout('layout_c')">A</a> | <a class="clickable" href="#" onclick="setLayout('layout_d')">B</a>
        </div>
<?php

    }

?>
    </div>
    <?php 
    
    
    if(!isset($this->contentRight)) {
        $layout = 'layout_a';
        $panel_1_content = $this->contentLeft;
        $panel_2_content = '';
        $panel_3_content = '';
    } elseif(!isset($this->contentRightBottom)) {
        $layout = 'layout_b';
        $panel_1_content = $this->contentLeft;
        $panel_2_content = $this->contentRight;
        $panel_3_content = '';
    } else {
        $layout = 'layout_c';
        $panel_1_content = $this->contentLeft;
        $panel_2_content = $this->contentRight;
        $panel_3_content = $this->contentRightBottom;
    }

    
    
    ?>
    <div id="content">
        <div id="panel_1" class="<?php print $layout; ?>"><div class="panel_inner"><?php print $panel_1_content; ?></div></div>
        <div id="panel_2" class="<?php print $layout; ?>"><div class="panel_inner"><?php print $panel_2_content; ?></div></div>
        <div id="panel_3" class="<?php print $layout; ?>"><div class="panel_inner"><?php print $panel_3_content; ?></div></div>
        <div id="resizehandle_1" class="<?php print $layout; ?>"></div>
        <div id="resizehandle_2" class="<?php print $layout; ?>"></div>
    </div>
    <div id="footer"><div id="copyright"><span>&copy; <?php print date('Y'); ?> <a href="http://www.picturae.com/" target="_blank">Picturae</a></span></div></div>
    <div id="modalbox_overlay"></div>
    <div id="modalbox"></div>
    <div id="message">asioudg aiudgasiodgailusdg liaug dliuasgd uiagd liausdg ilasudg ilu</div>
    <div id="spinner"><div></div></div>
</div>