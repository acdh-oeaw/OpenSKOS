<h1><?php

    if(!empty($this->titleValue)) {
        print sprintf($this->titleBase, '<span>' . $this->titleValue . '</span>');
    } else {
        print $this->titleBase;
    }

?></h1>
<a id="modalbox_closebutton" href="#" title="Sluiten" onclick="closeModalbox(); return false;" /><img src="images/icons/verwijder_clickable.gif" alt="" /></a>
<div id="modalbox_contentwrapper">
<?php

if(!isset($this->contentRight)) {

    // One column
?>
    <div id="modalbox_panel">
        <?php print $this->content1; ?>
        <div class="clear"></div>
    </div>
<?php
    
} else {

    // Two columns    
?>
    <div id="modalbox_panel_a">
        <?php print $this->content1; ?>
        <div class="clear"></div>
    </div>
    <div id="modalbox_panel_b">
        <?php print $this->content2; ?>
        <div class="clear"></div>
    </div>
<?php    
}
        
?>
    <div class="clear"></div>
</div>