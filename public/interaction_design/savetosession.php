<?php

    $type   = $_GET['type'];    
    $page   = $_GET['page'];
    $key    = $_GET['key'];
    $value  = $_GET['value'];
    
    if(!ini_get('session.auto_start')) session_start();
    $_SESSION[$type][$page][$key] = $value;

?>