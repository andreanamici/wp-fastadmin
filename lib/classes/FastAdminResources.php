<?php

namespace FastAdmin\lib\classes;

class FastAdminResources extends FastAdminCore
{
    /**
     * List of wp plugin's pages
     * 
     * @var array
     */
    protected $pages;
    
    /**
     * List of wp plugin's shortcuts
     * 
     * @var array
     */
    protected $shortcuts;
    
    public function wp_init()
    {
        $this->pages          = array();
        $this->pages['admin'] = require_once WP_FA_BASE_PATH_CONFIGS. '/pages_admin.php';
        
        add_action('admin_menu', array($this, 'admin_menu'));  
        
        add_action('wp_loaded', array($this, 'load_frontend_pages')); 
        
        add_action('wp_loaded', array($this, 'load_shortcuts'));

        $self  = $this;
        
        /**
         * Allow menu active for pages not present in menu
         */
        add_action('adminmenu_before', function($var) use($self){
            global $plugin_page;
            if(isset($_GET['page'])){
                $pageinfo    = $self->get_page($_GET['page'], 'admin');
                $parentpage  = !empty($pageinfo['parent_menu_slug']) ?  $pageinfo['parent_menu_slug'] : null;
                if($parentpage){
                    $plugin_page = strstr(WP_FA_PAGES_SLUG_PREFIX, $parentpage) === false ? WP_FA_PAGES_SLUG_PREFIX.$parentpage : $parentpage['menu_slug'];
                }
            }
        });
        
        $filters = require_once WP_FA_BASE_PATH_CONFIGS. '/filters.php';
        
        if(!empty($filters))
        {
            foreach($filters as $filter)
            {
                add_filter($filter['name'], $filter['callable'], isset($filter['priority']) ? $filter['priority'] : 10);
            }
        }
    }
    
    /**
     * Get all wp pages
     * 
     * @return array
     */
    public function get_pages($where)
    {
        return $this->pages[$where];
    }
    
    /**
     * Return a plugin page by name
     * 
     * @param string $page   page name
     * @param string $where  where add this page, admin/frontend, default "admin"
     * @return array
     */
    public function get_page($page, $where = 'admin')
    {        
        if(empty($where) || !in_array($where, array('admin','frontend')))
        {
            wp_die('For add a page to this plugin, please specify a valid $where');
        }
        
        $page_data = isset($this->pages[$where][$page]) ? $this->pages[$where][$page] : null;
        
        if(!$page_data)
        {
            $page      = str_replace(WP_FA_PAGES_SLUG_PREFIX,'',$page);
            $page_data = isset($this->pages[$where][$page]) ? $this->pages[$where][$page] : null;
        }
        
        $page_data['real_slug'] = WP_FA_PAGES_SLUG_PREFIX.$page;
        
        return $page_data;
    }
    
    /**
     * Add a single page to plugin
     * 
     * @param string $name  page name
     * @param string $where where add this page, admin/frontend, default "admin"
     * @param array  $info  page info for submenu infos
     * 
     * @return FastAdminResources
     */
    public function add_page($name, $where, array $info)
    {
        $this->pages[$where][$page] = $info;
        return $this;
    }
    
    /**
     * Load admin menu on wp hook "admin_menu"
     * 
     * @return $this
     */
    public function admin_menu()
    {
        $this->load_backend_menu()
             ->load_backend_pages();
        
        return $this;
    }
    
    /**
     * Load plugin admin pages
     * 
     * @return FastAdminResources
     */
    public function load_backend_pages()
    {    
        
        if($this->pages['admin'] )
        {
            foreach($this->pages['admin']  as $menu_slug => $submenu_page)
            {
                $submenu_page['menu_slug'] = !empty($submenu_page['menu_slug'])   ? $submenu_page['menu_slug'] : $menu_slug;                
                $this->_add_submenu_page($submenu_page);
            }
        }
        
        
        return $this;
    }
    
    /**
     * Load plugin admin menu and submens
     * 
     * @return FastAdminResources
     */
    public function load_backend_menu() 
    {
        $menu_pages = include WP_FA_BASE_PATH_CONFIGS. '/menu.php';
        
        if(!empty($menu_pages))
        {
            foreach($menu_pages as $menu_page)
            {
                $this->_add_menu_page($menu_page);
                
                if(!empty($menu_page['submenu_pages']))
                {
                    foreach($menu_page['submenu_pages'] as $submenu_page)
                    {
                       $this->_add_submenu_page($submenu_page, $menu_page);
                    }
                }
            }
        }
        
        return $this;
    }
    
    /**
     * Load plugin frontend pages
     * 
     * @return FastAdminResources
     */
    public function load_frontend_pages()
    {
        $this->pages['frontend'] = require_once WP_FA_BASE_PATH_CONFIGS. '/pages_frontend.php';
        
        if($this->pages['frontend'])
        {
//            $fa = fa_get();
//            add_action( 'wp',  function() use ($fa){
//                $fa->run();
//            });
        }
        
        return $this;
    }
    
    /**
     * Load plugin's shortcuts in wp
     * 
     * @return FastAdminResources
     */
    public function load_shortcuts()
    {
        $this->shortcuts = require_once WP_FA_BASE_PATH_CONFIGS . '/shortcuts.php';
        
        foreach($this->shortcuts as $name => $shortcut)
        {
           add_shortcode($name,$shortcut['callable']);
        }
        
        return $this;
    }
    
    /**
     * Render layout template
     * 
     * @param string $page      template page path
     * @param array  $data      array data
     * 
     * @return string
     */
    public function render_resource($page, array $data = array())
    {                                
        @ob_start();
                
        $this->include_resource($page, $data);
        
        $output = ob_get_clean();
        
        return $output;
    }
    
    public function render_messages($content)
    {
        if(isset($_SESSION['message'])){
            $content = $_SESSION['message']."<br><br>".$content;
        }
        
        return $content;
    }
    
    /**
     * Include resource
     * 
     * @param string $page      template page path
     * @param array  $data      array data
     * 
     * @return boolean
     */
    public function include_resource($page, array $data = array())
    {
        $file_path = WP_FA_BASE_PATH_RESOURCES. '/' . $page;
        
        if(!strstr($file_path,'.php'))
        {
            $file_path.='.php';
        }
        
        if(!file_exists($file_path))
        {
            return false;
        }
        
        extract($data);
        
        require $file_path; 
        
        return true;
    }   
    
    /**
     * Add a wp menu page
     * 
     * @param array $menu_page
     * 
     * @return string|bool
     */
    protected function _add_menu_page(array $menu_page)
    {
        $menu_page['callable'] = isset($menu_page['callable']) ? $menu_page['callable'] : $this->pages['admin'][$menu_page['menu_slug']]['callable'];
        
        if(!strstr($menu_page['menu_slug'], WP_FA_PAGES_SLUG_PREFIX))
        {
            $menu_page['menu_slug'] = WP_FA_PAGES_SLUG_PREFIX.$menu_page['menu_slug'];
        }
                
        return add_menu_page($menu_page['page_title'],$menu_page['menu_title'], $menu_page['capability'],$menu_page['menu_slug'],$menu_page['callable'],!empty($menu_page['icon']) ? $menu_page['icon'] : '',!empty($menu_page['position']) ? $menu_page['position'] : null);
    }
    
    /**
     * Add a wp menu page submenus
     * 
     * @param array $submenu_page   submenu page
     * @param array $menu_page      menu page
     * 
     * @return string|bool
     */
    protected function _add_submenu_page(array $submenu_page, array $menu_page = array())
    {
        $submenu_slug   = $submenu_page['menu_slug'];
        
        $submenu_page['callable'] = isset($submenu_page['callable']) ? $submenu_page['callable'] : $this->pages['admin'][$submenu_slug]['callable'];
                
        $parent_slug    = !empty($submenu_page['parent_slug']) ? $submenu_page['parent_slug'] : (!empty($menu_page['menu_slug']) ? $menu_page['menu_slug'] : null);
        $submenu_title  = !empty($submenu_page['menu_title'])  ? $submenu_page['menu_title']  : null;
        $capability     = !empty($submenu_page['capability'])  ? $submenu_page['capability']  : null;

        if(strstr($submenu_slug, WP_FA_PAGES_SLUG_PREFIX)  === false)
        {
            $submenu_slug = WP_FA_PAGES_SLUG_PREFIX.$submenu_slug;
        }

        if(strstr($parent_slug, WP_FA_PAGES_SLUG_PREFIX) === false)
        {
            $parent_slug = WP_FA_PAGES_SLUG_PREFIX.$parent_slug;
        }

        return add_submenu_page($parent_slug,$submenu_page['page_title'],$submenu_title,$capability,$submenu_slug,$submenu_page['callable']);
    }
}
