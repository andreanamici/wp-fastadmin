<?php

if(!function_exists('fa_cookie_set'))
{
    function fa_cookie_set($name, $value, $expiration = 0, $path = '/', $domain = null, $secure = false, $httponly = false, $samesite = 'None')
    { 
        $domain = $domain ? $domain : WP_FA_SESSION_DOMAIN;
        return setcookie($name, $value, [
            'expires' => $expiration,
            'path' => $path,
            'domain' => $domain ?? '.'.$_SERVER['SERVER_NAME'],
            'secure' => $samesite == 'None' ? true : $secure,
            'httponly' => $httponly,
            'samesite' => $samesite ?? 'None'
        ]);
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
    function fa_cookie_delete($name,$options = [])
    { 
        return setcookie($name, null,array_merge([
            'expires' => time()-3600,
            'path' =>  '/',
            'httponly' => false,
            'domain' => '.'.WP_FA_SESSION_DOMAIN,
            'samesite' => 'None',
            'secure' => true
        ], $options));
    }
}