<?php

if(!function_exists('fa_get_current_user_data'))
{
    /**
     * Return current wp user screen name or FALSE
     * 
     * @return mixed
     */
    function fa_get_current_user_data($data_property = null)
    {
        $current_user = wp_get_current_user();
        return $current_user ? !empty($data_property) ? $current_user->data->$data_property : $current_user->data : FALSE;
    }
}