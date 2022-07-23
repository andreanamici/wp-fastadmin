<?php


if(!function_exists('fa_session_get'))
{
    /**
     * Get data from session
     * @param string $key index
     * @param mixed  $default  default value, default null
     * @return mixed
     */
    function fa_session_get($key, $default = null)
    {
        if(!$key){
            return fa_get('session')->get($key, $default);
        }
    }
}


if(!function_exists('fa_session_set'))
{
    /**
     * Set data from session
     * @param string $key index
     * @param mixed  $value value setted
     * @return bool
     */
    function fa_session_set($key, $value)
    {
        return fa_get('session')->set($key, $value);
    }
}


if(!function_exists('fa_session_all'))
{
    /**
     * Retrive all data from session
     * @return array
     */
    function fa_session_all()
    {
        return fa_get('session')->all();
    }
}