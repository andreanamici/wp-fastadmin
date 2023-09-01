<?php


if(!function_exists('fa_get'))
{
    /**
     * Return fastadmin wrapper instance or property
     * 
     * @global type $fa
     * 
     * @param string $property property | null (fa object)
     * 
     * @return mixed
     */
    function fa_get($property = null) 
    {
        global $fa;
        
        return $property ? $fa->{$property} : $fa;
    }
}

if(!function_exists('fa_set'))
{
    /**
     * Set fastadmin property
     *
     * @param string $property property | null (fa object)
     * 
     * @return mixed
     */
    function fa_set($property, $value) 
    {
        fa_get()->{$property} = $value;
    }
}


if(!function_exists('fa_get_resources'))
{
    /**
     * Perfect Survey views
     * 
     * @global FastAdmin $ps
     * 
     * @return \FastAdmin\lib\classes\FastAdminResources
     */
    function fa_get_resources()
    {
        return fa_get('resources');
    }
}


if(!function_exists('fa_resource_render'))
{
    /**
     * render page
     * 
     * @param string $page  page path
     * @param array  $data  data
     * 
     * @return string
     */
    function fa_resource_render($page, array $data = array())
    {
        global $fa;
        return $fa->resources->render_resource($page, $data);
    }
}

if(!function_exists('fa_resource_include'))
{
    /**
     * include page
     * 
     * @param string $page  page path
     * @param array  $data  data
     * 
     * @return string
     */
    function fa_resource_include($page, array $data = array())
    {
        global $fa;
        return $fa->resources->include_resource($page, $data);
    }
}

if(!function_exists('fa_empty_str'))
{
    /**
     * Check for empty string allowing for a value of `0`
     * 
     * @param $str string
     * 
     * @return bool
     */
    function fa_empty_str($str) 
    {
        return !isset($str) || $str === "";
    }
}

if(!function_exists('fa_plugin_url'))
{
    /**
     * get plugin resource url
     * 
     * @param string $url url  
     * 
     * @return string
     */
    function fa_plugin_url($url)
    {
        if(strstr(__DIR__, 'wp-content/themes')){
            return get_template_directory_uri()  .'/'. WP_FA_PLUGIN_DIRNAME . '/' . $url;
        }

        return plugins_url(WP_FA_PLUGIN_DIRNAME . '/' . $url);
    }
}

if(!function_exists('fa_register_css'))
{
    /**
     * Register css
     * 
     * @param string $name  css file name
     * @param string $url   css file url
     */
    function fa_register_css($name, $url = null)
    {
        wp_register_style($name, fa_plugin_url($url));
        return wp_enqueue_style($name);
    }
}

if(!function_exists('fa_register_js'))
{
    /**
     * Register js
     * 
     * @param string $name      js file name
     * @param string $url       js file url
     * @param mixed  $relavite  if path is relative
     * @param array  $deps      an array of registered script handles this script depends on.
     * @param mixed  $ver       string specifying script 
     * 
     * @return mixed
     */
    function fa_register_js($name, $url = null, $relative = true, $deps = array(), $version = false, $footer = false)
    {
        $url = $relative ? fa_plugin_url($url) : $url;
        
        return wp_enqueue_script($name, $url, array(), $version, $footer);
    }
}

if(!function_exists('fa_get_ip'))
{
    /**
     * Return remote address
     * 
     * @return string
     */
    function fa_get_ip()
    {
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) 
        {
            $ipAddress = array_pop(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
        }
        
        return $ipAddress;
    }
}

if(!function_exists('fa_get_browser'))
{
    /**
     * Return user agent
     * 
     * @return string
     */
    function fa_get_browser()
    {
        return !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : WP_FA_UNKNOW_RESULT;
    }
}

if(!function_exists('fa_get_http_referer'))
{
    /**
     * Return current referer
     * 
     * @return string
     */
    function fa_get_http_referer()
    {
        return !empty($_REQUEST['referer']) ? $_REQUEST['referer'] : (!empty($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : WP_FA_UNKNOW_RESULT);
    }
}


if(!function_exists('fa_get_locale'))
{
    /**
     * Return current WP locale
     * @return string
     */
    function fa_get_locale()
    {
        return fa_get('locale');
    }
}


if ( ! function_exists('fa_html_escape'))
{
    /**
     * Returns HTML escaped variable.
     *
     * @param	mixed	$var		The input string or array of strings to be escaped.
     * @param	bool	$double_encode	$double_encode set to FALSE prevents escaping twice.
     * @return	mixed			The escaped string or array of strings as a result.
     */
    function fa_html_escape($var, $double_encode = TRUE)
    {
            if (empty($var))
            {
                    return $var;
            }
            if (is_array($var))
            {
                    foreach (array_keys($var) as $key)
                    {
                            $var[$key] = fa_html_escape($var[$key], $double_encode);
                    }
                    return $var;
            }
            return htmlspecialchars($var, ENT_QUOTES, 'UTF-8', $double_encode);
    }
}

if(!function_exists('_f'))
{
    /**
     * Translate text for "WP_FA_LANGUAGES_DOMAIN" textdomain
     * 
     * @param string $text
     * 
     * @return string
     */
    function _f($text, $context = null)
    {
        if($context){
            return _x($text, $context, WP_FA_LANGUAGES_DOMAIN);
        }

        return __($text, WP_FA_LANGUAGES_DOMAIN);
    }
}

if(!function_exists('_ef'))
{
    /**
     * Echo translated text for "WP_FA_LANGUAGES_DOMAIN" textdomain
     * 
     * @param string $text
     * @param string $context, default null
     * 
     * @return void
     */
    function _ef($text, $context = null)
    {
        echo _f($text, $context);
    }
}