<?php

use FastAdmin\actions\hooks\WpSettings;

if(!function_exists('fa_get_options'))
{
    function fa_get_options($group = WpSettings::SETTINGS_GROUP)
    {
        return get_option($group);
    }
}

if(!function_exists('fa_get_option'))
{
    function fa_get_option($name, $default = null)
    {        
        $options = fa_get_options();
        
        if($options && array_key_exists($name, $options)){
            return $options[$name];
        }

        return $default;
    }
}