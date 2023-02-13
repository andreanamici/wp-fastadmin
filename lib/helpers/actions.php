<?php



if(!function_exists('fa_action_callable'))
{
    /**
     * Build a callable class for wp menu and submenu callable variable
     * 
     * @param string $name      function or class name
     * @param string $action    method of class, if is not a class, wil passed to the function as first paramter
     * @param array  $params    params array
     * 
     * @return \Closure
     */
    function fa_action_callable($name, $action = null, $params = array())
    {
        if(!is_callable($name) && !function_exists($name) && !class_exists($name))
        {
            wp_die('Cannot use name'.var_export($name,true).' is not a valid callable!');
        }
        
        return function($data = null) use ($name, $action, $params){
                    
                    $callable   = $name;
                    $parameters = array();
                    $obj        = null;
                    
                    if($data && !is_array($data)){
                        $parameters = array($data);
                    }

                    if(!$params){
                        $parameters = func_get_args(); 
                    }

                    if(function_exists($name))
                    {
                        $parameters = array_merge($parameters, $action ? array($action) + $params : $params);
                    }
                    else if(class_exists($name))
                    {       
                        $obj = new $name();
                     
                        if($obj instanceof FastAdmin\lib\classes\FastAdminActions)
                        {
                            fa_set('actions',$obj);
                            $obj->wp_init();
                            $obj->wp_pre_dispatch();
                        }

                        if(!empty($params))
                        {
                            foreach($params as $key => $fieldname)
                            {
                                $regexp = null;

                                if(is_string($key))
                                {
                                    $regexp    = $fieldname;
                                    $fieldname = $key;
                                }

                                if($regexp)
                                {
                                    $value = isset($_GET[$fieldname]) ? $_GET[$fieldname] : null;
                                    
                                    if($value === null || !preg_match($regexp, $value))
                                    {
                                        wp_die('This page require param "'.$fieldname.'" that not match regexp: '.$regexp);
                                    }
                                }

                                $parameters[] = isset($_GET[$fieldname]) ? $_GET[$fieldname] : null;
                            }                        
                        }
                        
                        $callable = $obj && $action && method_exists($obj, $action) ? array($obj, $action) : $obj;
                    }
                        
                    $res = call_user_func_array($callable, $parameters);
                    
                    if($obj && $obj instanceof FastAdmin\lib\classes\FastAdminActions)
                    {
                        $res = $obj->wp_post_dispatch($res);
                    }
                    
                    return $res;
               };
    }
}


if(!function_exists('fa_action_path'))
{
    /**
     * Build a callable class for wp menu and submenu callable variable
     * 
     * @param string $action    action slug name
     * @param array  $params    params array
     * @param string $where     where, frontend/backend, default null (auto detected)
     * 
     * @return string
     */
    function fa_action_path($action, $params = array(), $where = null)
    {
        return call_user_func_array(array(fa_get('actions'),'get_action_path'), func_get_args());
    }
}


if(!function_exists('fa_action_url'))
{
    /**
     * Get wp action url
     * 
     * @param string $action    action slug name
     * @param array  $params    params array
     * @param string $where     where, frontend/backend, default null (auto detected)
     * 
     * @return string
     */
    function fa_action_url($action, $params = array(), $where = null)
    {
        return call_user_func_array(array(fa_get('actions'),'get_action_url'),   func_get_args());
    }
}


if(!function_exists('fa_page_redirect'))
{
    /**
     * Redirect to a page action
     * 
     * @param string $action      action
     * @param array  $params      query string params
     * @param int    $status      http status, default 302
     * 
     * @return void
     */
    function fa_page_redirect($action, array $params = array(), $status = 302)
    {
        $path = fa_action_path($action, $params);
        return fa_redirect($path, $status);
    }
}


if(!function_exists('fa_redirect'))
{
    /**
     * Redirect to location
     * 
     * @param string $location    location to redirect
     * @param int    $status      http status, default 302
     * 
     */
    function fa_redirect($location, $status = 302)
    {
        wp_redirect($location, $status);
        exit;
    }
}


if(!function_exists('fa_page_redirect_with_message'))
{
    /**
     * Redirect to location
     * 
     * @param string $location    location to redirect
     * @param array  $params      http query params
     * @param string $type        message type
     * @param string $text        message text
     * @param int    $status      http status, default 302
     * 
     */
    function fa_page_redirect_with_message($page, array $params, $type, $text, $status = 302)
    {
        fa_message_set($type, $text);
        fa_page_redirect($page, $params, $status);
    }
}

if(!function_exists('fa_redirect_with_message'))
{
    /**
     * Redirect to location
     * 
     * @param string $location    location to redirect
     * @param string $type        message type
     * @param string $text        message text
     * @param int    $status      status code, default 302
     * 
     */
    function fa_redirect_with_message($location, $type, $text, $status = 302)
    {
        fa_message_set($type, $text);
        fa_redirect($location, $status);
    }
}