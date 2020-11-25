<?php

return array(
    
    'main' => array(
        
        'page_title'    => 'Gestionale',
        'menu_title'    => WP_FA_EMAIL_SITE_NAME,
        'capability'    => 'manage_options',
        'menu_slug'     => 'main',
        'callable'      => '',
        'icon'          => 'dashicons-clipboard',
        'position'      => 80,

        'submenu_pages' => array(

            //   array(
            //         'page_title' => '<Menu Item Name>',
            //         'menu_title' => '<Menu Title>',
            //         'capability' => '<permission> eg: manage_options', 
            //         'menu_slug'  => '<slug>, eg: page_name'
            //   ),
        )
        
    )
    
);
