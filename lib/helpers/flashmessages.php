<?php

if(!function_exists('fa_message'))
{
    function fa_message()
    {
        $message = fa_message_get();
        echo $message;
    }
}

if(!function_exists('fa_message_get'))
{
    function fa_message_get()
    {
        $message = fa_get('session')->flash_get('message');
        
        if($message)
        {
            return fa_resource_render('common/layout/flashmessages', array('message' => $message));
        }
        
        return "";
    }
}

if(!function_exists('fa_message_has'))
{
    function fa_message_has()
    {
        return fa_get('session')->flash_has('message');
    }
}

if(!function_exists('fa_message_set'))
{
    function fa_message_set($type, $text)
    {
        return fa_get('session')->flash_set('message', array('type' => $type,'text' => $text));
    }
}