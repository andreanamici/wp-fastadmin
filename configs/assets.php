<?php

/**
 * Assets js, css, inline assets whit custom functions to include for admin and frontend layouts
 */
return array(
    
    'admin' => array(
        'fa_admin_inline'                 => function(){
                global $post;
                $content =  '<script type="text/javascript">var wp_locale = "'. get_locale() .'";</script>';
                $content.=  '<script type="text/javascript">var wp_post = '.($post ? json_encode($post) : '{ID : null }').';</script>';
                $content.=  '<script type="text/javascript">var ajaxurl = "' . admin_url('admin-ajax.php') . '";</script>';
                $content.=  '<script type="text/javascript">var pageurl = "' . admin_url('admin.php') . '";</script>';
                return $content;
        },
        'fa_admin_translations'           => function(){
                $translations = array(
                    'Cancel' => _f('Cancel'),
                    'Confirm'=> _f('Confirm')
                );
                $content = '<script type="text/javascript">var fa_translations = '.json_encode($translations).';</script>';
                return $content;
        },
        'fa_admin_common_jquery_ui_css'   => array('type' => 'css' ,'url' => 'resources/common/assets/css/jquery-ui.min.css'),
        'fa_admin_jquery_timepicker_css'  => array('type' => 'css',  'url' => 'resources/common/assets/css/jquery.timepickeraddon.css'),
        'fa_admin_common_css'             => array('type' => 'css', 'url' =>  'resources/common/assets/css/fa.css'),
        'fa_admin_css'                    => array('type' => 'css', 'url' =>  'resources/admin/assets/css/fa.css'),
        'fa_admin_common_jquery_ui_js'    => array('type' => 'js',  'url' => 'resources/common/assets/js/jquery-ui.min.js'),
        'fa_admin_common_jquery_ui_cmb'   => array('type' => 'js',  'url' => 'resources/common/assets/js/jquery-ui-combobox.min.js'),
        'fa_admin_jquery_timepicker_js'   => array('type' => 'js',  'url' => 'resources/common/assets/js/jquery.timepickeraddon.js'),
        'fa_admin_common_js'              => array('type' => 'js', 'url' => 'resources/admin/assets/js/fa-common.js'),
        'fa_admin_main_js'                => array('type' => 'js', 'url' => 'resources/admin/assets/js/fa-main.js'),
    ),
    
    'frontend' => array(
        'fa_frontend_common_css'     => array('type' => 'css', 'url' =>  'resources/common/assets/css/fa.css'),
        'fa_frontend_css'            => array('type' => 'css', 'url' =>  'resources/frontend/assets/css/fa.css')
    )
    
);