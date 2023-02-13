<?php

namespace FastAdmin\lib\classes;

/**
 * Class for hooks registration
 */
class FastAdminHooks extends FastAdminCore
{
    const DEFAULT_PRIORITY = 10;

    const DEFAULT_ACCEPTED_ARGS = 10;

    protected $hooks = array();

    protected $actions = [];

    public function wp_init()
    {                        
        $this->hooks = require_once WP_FA_BASE_PATH_CONFIGS . '/hooks.php';
               
        if(!empty($this->hooks))
        {
            foreach($this->hooks as $name => $callables)
            {
                if(is_callable($callables)){
                    $this->addAction($name, $callables);    
                }else if(is_array($callables)){
                    foreach($callables as $callable){
                        $this->addAction($name, $callable);
                    }
                }
            }
        }
    }

    public function addAction($name, $callable)
    {
        $priority = self::DEFAULT_PRIORITY;
        $accepted_args = self::DEFAULT_ACCEPTED_ARGS;

        if(!is_callable($callable) && is_array($callable)){
            if(!isset($callable['callable'])){
                wp_die('Hook config error, missed property "callable" for hook '. $name);
            }
            $priority          = isset($callable['priority']) ? $callable['priority'] : $priority;
            $accepted_args     = isset($callable['accepted_args']) ? $callable['accepted_args'] : self::DEFAULT_ACCEPTED_ARGS;
            $callable          = $callable['callable'];
        }

        if(!is_callable($callable)){
            wp_die('Hook config error, invalid callable for hook '. $name);
        }

        if(!isset($this->actions[$name])){
            $this->actions[$name] = 0;
        }

        $priority = $priority + (++$this->actions[$name]);

        return add_action($name, $callable, $priority, $accepted_args);
    }
}