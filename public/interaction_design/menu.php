<?php

    $menu = array();
    
    // Drie niveaus. Opbouw:
    // $menu['n1']['_label'] 
    // $menu['n1'][n2]['_label']
    // $menu['n1'][n2][n3]['_label']

    $menu['management']['_label']                                               = 'Management';
    $menu['management']['search']['_label']                                     = 'Search, browse and edit';
    $menu['management']['users']['_label']                                      = 'Users and rights';
    $menu['management']['reports']['_label']                                    = 'Reports';
    $menu['management']['configuration']['_label']                              = 'Configuration';
    $menu['management']['configuration']['conceptschemes']['_label']            = 'Concept schemes';
    $menu['management']['configuration']['languages']['_label']                 = 'Languages';
