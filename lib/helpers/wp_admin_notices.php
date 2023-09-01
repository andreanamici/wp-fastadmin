<?php


if(!defined('fa_wp_get_admin_notice'))
{
    function fa_wp_get_admin_notice($type)
    {
        $option = get_option('wp_admin_notice_'.$type);
        update_option('wp_admin_notice_'.$type, null);
        return $option;
    }
}

if(!defined('fa_wp_set_admin_notice'))
{
    function fa_wp_set_admin_notice($type, $message)
    {
        return update_option('wp_admin_notice_'.$type, $message);
    }
}
