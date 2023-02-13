<?php

namespace FastAdmin\lib\classes;

/**
 * Class for actions
 */
class FastAdminActions extends FastAdminCore
{
    /**
     * Model of FastAdmin
     * 
     * @var FastAdminModel
     */
    protected $model;
    
    /**
     * Resources layout dir name
     * 
     * @var string
     */
    protected $layout = 'admin';
    
    /**
     * Session lib
     * 
     * @var FastAdminSession
     */
    protected $session;
  
    /**
     * Current fa page
     * @var strig
     */
    protected $page;
    
    /**
     * Current fa page info data
     * @var Array
     */
    protected $page_info;
    
    /**
     * All wp pages registered
     * @var array
     */
    protected $pages;
    
    
    public function __construct()
    {
        
    }


    public function wp_init()
    {         
        $this->_wp_add_ajax_actions(); 
        $this->model     = fa_get('model');
        $this->session   = fa_get('session');    
    }
    

    public function wp_register_ajax_actions()
    {
        $ajax_actions = fa_get('resources')->get_pages('ajax');

        foreach($ajax_actions as $action => $info)
        {
            if(empty($info['logged']))
            {
                $wp_action_name = 'wp_ajax_nopriv_'.$action;                
                add_action($wp_action_name, $info['callable']);
                
                $wp_action_name = 'wp_ajax_'.$action;                
                add_action($wp_action_name, $info['callable']);
            }
            else
            {
                $wp_action_name = 'wp_ajax_'.$action;                
                add_action($wp_action_name, $info['callable']);
            }
        }   
    }
    /**
     * Called on pre dispatch
     */
    public function wp_pre_dispatch()
    {
        $this->page       = isset($_GET['page']) ? $_GET['page'] : null;
        $this->page_info  = fa_get('resources')->get_page($this->page,$this->layout);        
        $this->pages      = fa_get('resources')->get_pages($this->layout);
        $this->pages_main = array_filter($this->pages, function($value){ return !empty($value['mainpage']); });
    }
    
    /**
     * Called after fastadmin action execution
     * 
     * @param mixed $response action response
     * 
     * @return mixed
     */
    public function wp_post_dispatch($response)
    {
        return $response;
    }
    
    /**
     * Return current page name
     * @return array
     */
    public function get_page()
    {
        return $this->page;
    }
    
    /**
     * Return all pages registered with plugin
     * @return array
     */
    public function get_pages()
    {
        return $this->pages;
    }
    
    /**
     * Return current layout
     * @return string
     */
    public function get_layout()
    {
        return $this->layout;
    }
    
    /**
     * Set current layout
     * 
     * @param string $layout
     * 
     * @return $this
     */
    public function set_layout($layout)
    {
        $this->layout = $layout;
        return $this;
    }
    
    /**
     * Set data before render
     * 
     * @param array $data current data
     * 
     * @return string
     */
    protected function _pre_response(array $data)
    {
        $data['logged_user'] = wp_get_current_user();
        
        if(empty($data['title']))
        {
            $data['title'] = isset($this->page_info['page_title']) ? $this->page_info['page_title'] : '';
        }
        
        if(empty($data['subtitle']) && isset($data['listing']) && $data['listing'] instanceof FastAdminListTable)
        {
            $data['subtitle'] = $data['listing']->get_total_items().' record trovati';
        }
        
        return $data;
    }
    
    /**
     * Render view and send response to wp
     * 
     * @param string $view      view path from resources dir
     * @param array  $data      data to set for views, default array()
     * @param bool   $return    return rendered string or output to browser, default output
     * 
     * @return mixed
     */
    public function render($view, array $data = array(), $return = false)       
    {
        $data = $this->_pre_response($data);
        return  $return ? fa_resource_render($this->layout.'/' .$view, $data) : fa_resource_include($this->layout.'/'. $view, $data);
    }
    
    
    /**
     * Send JSON response to browser
     * 
     * @param mixed $data
     * @param int   $status_code
     * @parma bool  $pre_response
     * 
     */
    public function ajax_response($data, $status_code = 200, $pre_response = true)
    {
        if($pre_response){
            $data = $this->_pre_response($data);
        }
        
        @ob_end_clean();
        return $status_code == 200 ? wp_send_json_success($data) : wp_send_json_error($data, $status_code);
    }
    
    /**
     * Generate url for frontend/backend
     * 
     * @param string $action    action
     * @param array  $params    paramters to pass GET
     * @param string $where     where, frontend/backend, default current layout
     * 
     * @return string
     */
    public function get_action_url($page, $params  = array(), $where = null)
    {
        $where = $where ? $where : $this->layout;
        
        if(isset($params['page'])){
            unset($params['page']);
        }
        
        $query_params = !empty($params) ? '&'.http_build_query($params) : '';
               
        $page      = strstr($page, WP_FA_PAGES_SLUG_PREFIX) === false ? WP_FA_PAGES_SLUG_PREFIX.$page : $page;
        $page      = strstr($page,':') != false ? explode(':',$page) : $page;
        $action    = '';
        
        if(is_array($page))
        {
            $action    = $page[1];
            $page      = $page[0];
            $action    = '&action='.$action;
        }
        
        
        return ($where == 'frontend' ? site_url('?page='.$page.$action.$query_params) : admin_url('admin.php?page='.$page.$action.$query_params));
    }
    
    /**
     * Generate url for frontend/backend
     * 
     * @param string $action    action
     * @param array  $params    paramters to pass GET
     * @param string $where     where, frontend/backend, default current layout
     * 
     * @return string
     */
    public function get_action_path($action, $params  = array(), $where = null)
    {
        return str_replace(site_url(), '', $this->get_action_url($action, $params, $where));
    }
    
    /**
     * Generate breadcrumbs list elemnts ready for render
     * 
     * @param array  $crumbs all pages
     * @param string $title  string pr current page title
     * 
     * @return array
     */
    public function get_breadcrumb(array $crumbs, $title = null)
    {
        $breadcrumb = array();
        
        if($this->layout == 'admin'){
            $breadcrumb[] = array(
                'title' => get_bloginfo(),
                'url'   => get_admin_url()
            );
        }

        foreach($crumbs as $crumb)
        {
           $crumb = is_array($crumb) ? $crumb : array('page' => $crumb);
           $crumb['parameters'] = isset($crumb['parameters']) ? $crumb['parameters'] : array();
           $page = isset($crumb['page']) ? fa_get_resources()->get_page($crumb['page']) : null;
           
           if(!$page)
           {
               //wp_die('Cannot use this crumb value: '.$page);
               continue;
           }
           
           $breadcrumb[] = array('title' => isset($crumb['title']) ? $crumb['title'] : $page['page_title'], 'url' =>  isset($crumb['url']) ? $crumb['url'] : fa_action_path($crumb['page'], $crumb['parameters']));
        }
        
        $title = $title ? $title : $this->page_info['page_title'];
        
        if($title)
        {
            $breadcrumb[] = array('title' => $title);
        }
        
        return $breadcrumb;
    }
    
    /**
     * Register asset
     * 
     * @param mixed  $asset string relative url or asset info array
     * @param string $name  unique asset id for wp
     * 
     * @return FastAdminActions
     */
    protected function _register_asset($asset, $name)
    {
        fa_get('assets')->register_asset($asset,$name);
        return $this;
    }
    
    /**
     * Register wp actions whith object publics method's
     * 
     * @param String $wp_action wp action prefix
     * 
     * @return FastAdminCore
     */
    protected function _wp_add_ajax_actions()
    {
        $reflectionObject = new \ReflectionClass(get_called_class());
        $publicMethods    = $reflectionObject->getMethods(\ReflectionMethod::IS_PUBLIC);

        if(!empty($publicMethods))
        {
            foreach($publicMethods as $publicMethod)/*@var $publicMethod ReflectionMethod*/
            {
                if(preg_match('/^ajax([A-z\_]+)$/',$publicMethod, $matches))
                {
                    $wp_action_name = 'wp_ajax_'.strtolower($matches[1]);
                    add_action($wp_action_name, array($this, $publicMethod->getName()));

                    $wp_action_name = 'wp_ajax_nopriv_'.strtolower($matches[1]);
                    add_action($wp_action_name, array($this, $publicMethod->getName()));
                }
            }
        }
        
        return $this;
    }
}
