<?php

if(!function_exists('fa_flash_get'))
{
    function fa_flash_get($key, $default = false)
    {
        return fa_get('session')->flash_get($key, $default);
    }
}

if(!function_exists('fa_flash_has'))
{
    function fa_flash_has($key)
    {
        return fa_get('session')->flash_has($key);
    }
}

if(!function_exists('fa_flash_set'))
{
    function fa_flash_set($key, $value)
    {
        return fa_get('session')->flash_set($key, $value);
    }
}