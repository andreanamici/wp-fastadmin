<?php

namespace FastAdmin\lib\classes;

/**
 * Class for hooks registration
 */
class FastAdminHooks extends FastAdminCore
{
    protected $hooks = array();

    protected $actions = [];

    public function wp_init()
    {                        
        $this->hooks = require_once WP_FA_BASE_PATH_CONFIGS . '/hooks.php';
               
        if(!empty($this->hooks))
        {
            foreach($this->hooks as $name => $callable)
            {
                $this->addAction($name, $callable);
            }
        }
    }

    public function addAction($name, $callable)
    {

        if(!is_callable($callable) && is_array($callable)){
            foreach($callable as $callfn){
                $this->addAction($name, $callfn);
            }
            return;
        }

        if(!is_callable($callable) && is_numeric($name)){
            if(!isset($callable['action'])){
                wp_die('Hook config error!');
            }
            $name     = $callable['action'];
            $callable = $callable['callable'];
        }

        if(!is_callable($callable)){
            wp_die('Hook config error!');
        }

        $this->actions[$name] = 0;

        $priority = 10 + $this->actions[$name]++;

        return add_action($name, $callable, $priority);
    }
}