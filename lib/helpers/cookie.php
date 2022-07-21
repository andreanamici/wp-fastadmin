<?php

if(!function_exists('fa_cookie_set'))
{
    function fa_cookie_set($name, $value, $expiration = 0, $path = '/', $domain = null, $secure = false, $httponly = false)
    { 
        $domain = $domain ? $domain : $_SERVER['SERVER_NAME'];
        return setcookie($name, $value, $expiration > 0 ? time()+$expiration : 0, $path, $domain, $secure, $httponly);
    }
}


if(!function_exists('fa_cookie_get'))
{
    function fa_cookie_get($name, $default = false)
    { 
        return array_key_exists($name, $_COOKIE) ? $_COOKIE[$name] : $default;
    }
}


if(!function_exists('fa_cookie_delete'))
{
    function fa_cookie_delete($name)
    { 
        return setcookie($name, null, -1, '/');
    }
}