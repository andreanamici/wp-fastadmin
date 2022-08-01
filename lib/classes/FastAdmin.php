<?php 

namespace FastAdmin\lib\classes;

require_once 'FastAdminCore.php';

class FastAdmin extends FastAdminCore
{   
    /**
     * Assets class
     * 
     * @var FastAdminAssets
     */
    public $assets;
    
    /**
     * Assets class
     * 
     * @var FastAdminSession
     */
    public $session;
    
    /**
     * Cron class
     * 
     * @var FastAdminCron
     */
    public $cron;
    
    /**
     * DB Class
     * 
     * @var FastAdminDB
     */
    public $db;
    
    /**
     * Resources Class
     * 
     * @var FastAdminResources
     */
    public $resources;
    
    /**
     * Model class
     * 
     * @var FastAdminModel
     */
    public $model;
    
    /**
     * Actions class
     * 
     * @var FastAdminActions
     */
    public $actions;


    /**
     * Hooks class
     * 
     * @var FastAdminHooks
     */
    public $hooks;
    
    /**
     * Current locale
     * @var string
     */
    public $locale;
    
    public function __construct()
    {        
        spl_autoload_register(array($this,'__autoload'));
    }
    
    /**
     * Register autoload
     * 
     * @param string $className class to load
     * 
     * @return $this
     */
    protected function __autoload($className)    
    {   
        if(strstr($className,'\\') != false)
        {
            $filePath = WP_FA_BASE_PATH. DIRECTORY_SEPARATOR . str_replace('\\',DIRECTORY_SEPARATOR,$className).'.php';
            
            $filePath = str_replace('FastAdmin/','',$filePath);

            if(file_exists($filePath))
            {
                require_once $filePath;
            }
        }
        
        return $this;
    }    
    
    /**
     * Booting plugin
     * 
     * @return $this
     */
    public function boot()
    {
        $this->load_config();

        add_action('init', array($this, 'wp_init'), 1);
                
        add_action('wp_loaded', array($this, 'run'),100);
        
        $this->locale = get_locale();
                
        return $this;
    }
    
    /**
     * Called after plugin boot, wp is not still loaded
     */
    public function start()
    {
    }
    
        
    /**
     * Call when plugin is running and WP is full loaded
     * 
     * @return void
     */
    public function run()
    {
        if(!empty($_REQUEST['page']))
        {
            if($action = $this->resources->get_page($_REQUEST['page'], is_admin() ? 'admin' : 'frontend'))
            {
                if(!empty($action['global']))
                {
                    $get = $_GET;
                    unset($get['page']);                
                    call_user_func_array($action['callable'], $get);
                    exit;
                }
            }
        }
    }
    
    
    public function wp_init() 
    {
        //enable output buffering for redirect in pages
        add_action('init', function(){ ob_get_level() <= 1 ? ob_start() : null; });
        
        if(get_option('timezone_string')){
            date_default_timezone_set(get_option('timezone_string'));
        }
                                                
        if(defined("WP_AUTO_UPDATE_CORE") && !WP_AUTO_UPDATE_CORE)
        {
            /* Disable all wp updates */
            add_action('init',function(){

                if(! current_user_can('update_core')){
                    return false;
                }

                add_filter('pre_option_update_core','__return_false');
                add_filter('pre_site_transient_update_core','__return_false');
                add_filter('auto_update_plugin','__return_false');
                add_filter('auto_update_theme','__return_false');
                remove_action( 'init', 'wp_version_check' ,2);
                add_filter('admin_init',function(){ 
                    remove_submenu_page( 'index.php', 'update-core.php' ); 
                    remove_action('admin_notices','update_nag',3);    
                });
            });
        }
        
        $this->load_textdomain()
             ->load_helpers()
             ->load_models()
             ->load_resources()
             ->load_assets()
             ->load_session()
             ->load_hooks()
             ->load_actions()
             ->load_cron()
        ;
    }
     
    private function load_config()
    {
        $envFile = WP_FA_BASE_PATH.'/.env';
        
        if(!file_exist($envFile)){
            wp_die('Wp Fastadmin: env file not found in '.$envFile);
        }

        $this->dotenv = new FastAdminDotEnv($envFile);
        $this->dotenv->load();

        require_once WP_FA_BASE_PATH_CONFIGS. '/config.php';

        return $this;
    }       

    /**
     * Load assets
     * 
     * @return FastAdmin
     */
    private function load_assets()
    {
        $this->assets  = new FastAdminAssets();
        $this->assets->wp_init();
        
        return $this;
    }
    
    /**
     * Load actions
     * 
     * @return FastAdmin
     */
    private function load_actions()
    {
        $this->actions = new FastAdminActions();
        $this->actions->wp_init();
        $this->actions->wp_register_ajax_actions();
        
        return $this;
    }

    private function load_hooks()
    {
        $this->hooks = new FastAdminHooks();
        $this->hooks->wp_init();
        
        return $this;
    }

    /**
     * Load text domains text
     * 
     * @return FastAdmin
     */
    private function load_session()
    {
        $this->session = new FastAdminSession();
        $this->session->wp_init();
        
        return $this;
    }
    
    /**
     * Load text domains text
     * 
     * @return FastAdmin
     */
    private function load_cron()
    {
        $this->cron = new FastAdminCron();
        $this->cron->wp_init();
        
        return $this;
    }
    
  
    
    /**
     * load db manager of plugin
     * 
     * @return FastAdmin
     */
    private function load_resources()
    {
        $this->resources = new FastAdminResources();
        $this->resources->wp_init();
        
        return $this;
    }
    
    /**
     * Load post type of survey
     * 
     * @return FastAdmin
     */
    private function load_models()
    {       
        $this->model = new FastAdminModel();
        $this->model->wp_init();
        
        return $this;
    }
    
    /**
     * Load text domains text
     * 
     * @return $this
     */
    private function load_textdomain()
    {
        $domain = WP_FA_LANGUAGES_DOMAIN;
        $localefile = WP_FA_BASE_PATH . '/languages/'.$domain.'-'.get_locale().'.mo';
        if(file_exists($localefile)){
            load_textdomain($domain , $localefile);
        }
        return $this;
    }
    
    /**
     * Include ps helpers files
     * 
     * @return FastAdmin
     */
    private function load_helpers()
    {
        $helpers_files = glob(WP_FA_BASE_PATH_HELPERS.'/*.php');
        
        if(!empty($helpers_files))
        {
            foreach($helpers_files as $file)
            {
                require_once $file;
            }
        }
        
        return $this;
    }
}
