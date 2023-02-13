<?php

if(!defined("INPUT_REQUEST")){
    define("INPUT_REQUEST", 3);
}

if(!defined("INPUT_SESSION")){
    define("INPUT_SESSION", 6);
}

if(!function_exists('fa_input_fetch'))
{
    /**
     * Fetch data from all INPUT_* vars
     *
     * @param string $type        global var type, INPUT_*
     * @param string $name        element of global var, default null (all)
     * @param mixed  $default     default value if $name is absent in $global, default false
     * @param bool   $xss_filter  apply XSS filtering, default true
     * 
     * @return mixed|bool
     */
    function fa_input_fetch($type, string $name = null, $default = false, $xss_filter = true)
    {
       $global = array();

       switch($type)
       {
           case INPUT_POST:     $global = $_POST;break;
           case INPUT_GET:      $global = $_GET;break;
           case INPUT_COOKIE:   $global = $_COOKIE;break;
           case INPUT_REQUEST:  $global = $_REQUEST;break;
           case INPUT_ENV:      $global = $_ENV;break;
           case INPUT_SERVER:   $global = $_SERVER;break;
           case INPUT_SESSION:  $global = $_SESSION;break;
       }

       if($xss_filter)
       {
          $global = filter_input_array($type,FILTER_SANITIZE_STRING);
       }

       if(is_null($name))
       {
           return $global;
       }

       if(empty($global) || !array_key_exists($name, $global))
       {
           return $default;
       }

       $var = $global[$name];

       switch(gettype($var))
       {
           case "boolean": $var = boolval($var);break;
           case "double":  $var = doubleval($var);break;
           case "float":   $var = floatval($var);break;
           case "string":  $var = sanitize_text_field($var);break;
           case "integer": $var = intval($var);break;
       }

       if(is_array($global[$name]))
       {
           return $global[$name];
       }

       return $var;
    }
}

if(!function_exists('fa_input_post'))
{
    /**
     * Fetch data from POST
     *
     * @param string $name        name
     * @param mixed  $default     default value
     * @param bool   $xss_filter  apply XSS filtering, default true
     * 
     * @return mixed|bool
     */
    function fa_input_post($name = null, $default = false, $xss_filter = true)
    {
        return fa_input_fetch(INPUT_POST,$name, $default, $xss_filter);
    }
}


if(!function_exists('fa_input_get'))
{
    /**
     * Fetch data from GET
     *
     * @param string $name      name
     * @param mixed  $default   default value
     * @param bool   $xss_filter  apply XSS filtering, default true
     * 
     * @return mixed|bool
     */
    function fa_input_get($name, $default = false, $xss_filter = true)
    {
        return fa_input_fetch(INPUT_GET, $name, $default, $xss_filter);
    }
}


if(!function_exists('fa_input_request'))
{
    /**
     * Fetch data from REQUEST
     *
     * @param string $name      name
     * @param mixed  $default   default value
     * @param bool   $xss_filter  apply XSS filtering, default true
     * 
     * @return mixed|bool
     */
    function fa_input_request($name, $default = false, $xss_filter = true)
    {
        return fa_input_fetch(INPUT_REQUEST,$name, $default, $xss_filter);
    }
}


if(!function_exists('fa_input_get_post'))
{
    /**
     * Fetch data from GET or POST
     *
     * @param string $name      name
     * @param mixed  $default   default value
     * @param bool   $xss_filter  apply XSS filtering, default true
     * 
     * @return mixed|bool
     */
    function fa_input_get_post($name, $default = false, $xss_filter = true)
    {
        $res = fa_input_get($name, $default, $xss_filter);

        if(!$res){
            $res = fa_input_post($name, $default, $xss_filter);
        }

        return $res;
    }
}

if(!function_exists('fa_input_session'))
{
    /**
     * Fetch data from SESSION
     *
     * @param string $name      name
     * @param mixed  $default   default value
     *
     * @return mixed|bool
     */
    function fa_input_session($name, $default = false)
    {
        return fa_input_fetch(INPUT_SESSION,$name, $default);
    }
}

if(!function_exists('fa_input_cookie'))
{
    /**
     * Fetch data from COOKIE
     *
     * @param string $name      name
     * @param mixed  $default   default value
     * 
     * @return mixed|bool
     */
    function fa_input_cookie($name, $default = false)
    {
        return fa_input_fetch(INPUT_COOKIE,$name, $default);
    }
}


if(!function_exists('fa_input_server'))
{
    /**
     * Fetch data from SERVER
     *
     * @param string $name      name
     * @param mixed  $default   default value
     *
     * @return mixed|bool
     */
    function fa_input_server($name, $default = false)
    {
        return fa_input_fetch(INPUT_SERVER,$name, $default);
    }
}


if(!function_exists('fa_input_env'))
{
    /**
     * Fetch data from ENV
     *
     * @param string $name      name
     * @param mixed  $default   default value
     * @param bool   $xss_filter  apply XSS filtering, default true
     * 
     * @return mixed|bool
     */
    function fa_input_env($name, $default = false, $xss_filter = true)
    {
        return fa_input_fetch(INPUT_ENV,$name, $default, $xss_filter);
    }
}
