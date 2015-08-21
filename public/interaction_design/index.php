<?php

//    if(function_exists('isPictura')) {
//	    if(!isPictura()) {
//	    	exit('Interactieontwerp niet beschikbaar vanaf uw lokatie.');
//	    }
//    }

    function __autoload($class_name) {
        require_once 'lib/' . $class_name . '.php';
    }

    if(!ini_get('session.auto_start')) session_start();
    
    $page = new Page();