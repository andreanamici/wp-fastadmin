<?php

/**
 * Define here wp ajax actions for logged and unlogged users
 * 
 * When plugin is running, you can call Ajax using wp-admin.php?action=<action> and register a Controller and method 
 */
return array(

    /**
     * echo action
     */
    'echo' => array(
        'capability' => 'edit_posts',
        'callable'   => fa_action_callable('FastAdmin\actions\admin\Dashboard','echo'),
    ),
);