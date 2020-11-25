<?php

/**
 * Define here wp pages actions
 * 
 * When plugin is running, you can use admin.php?page=<action> and register a Controller and method 
 */
return array(
    
    /**
     * Main Fast Admin Page
     */
    'main' => array(
        'page_title' => translate('Dashboard'),
        'capability' => 'edit_posts',
        'callable'   => fa_action_callable('FastAdmin\actions\admin\Dashboard','index'),
    ),


    // '<slug>' => array(
    //     'page_title'        => translate('Menu item'),
    //     'capability'        => 'edit_posts',
    //     'parent_menu_slug'  => '<parentslug>',
    //     'callable'          => fa_action_callable('<object>,'<method>'),
    // ),
    
);