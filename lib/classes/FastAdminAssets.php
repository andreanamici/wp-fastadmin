<?php

namespace FastAdmin\lib\classes;

class FastAdminAssets extends FastAdminCore
{   
    /**
     * List of all assets
     * @var array
     */
    protected $assets = array();
    
    public function wp_init()
    {                        
        $this->assets = require_once WP_FA_BASE_PATH_CONFIGS . '/assets.php';
                
        if(!empty($this->assets))
        {
            add_action('admin_head', array($this,'register_assets_admin'));        
            
            add_action('wp_head',array($this, 'register_assets_frontend'));
        }
    }
    
    public function register_asset($asset, $name)
    {
        $ret = 0;
        
        if(is_callable($asset))
        {
            $content = call_user_func_array($asset, array($this, $name));
            if(is_string($content) && !empty($content)){
                echo $content;
                $ret = 1;
            }
        }
        else
        {
            if(is_string($asset))
            {
                $path_info = pathinfo($asset);

                $asset = array(
                    'url'    => $asset,
                    'type'   => $path_info['extension']
                );
            }
            
            
            switch($asset['type'])
            {
                case 'css':
                    $ret = fa_register_css($name,$asset['url']) ? 1 : 0;
                break;
                case 'js': 
                    $ret = fa_register_js($name,$asset['url']) ? 1 : 0;
                break;
            }
        }
        
        return $ret;
    }
    
    public function register_assets_frontend()
    {
        return $this->_register_assets('frontend');
    }
    
    public function register_assets_admin()
    {
        return $this->_register_assets('admin');   
    }
    
    protected function _register_assets($where)
    {
        $registers = 0;
        
        if(!empty($this->assets[$where]))
        {
            foreach($this->assets[$where] as $name => $asset)
            {
                $registers+= $this->register_asset($asset, $name);
            }
        }
        
        return $registers;
    }
}
