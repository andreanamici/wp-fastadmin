<?php

namespace FastAdmin\actions;

class Cron extends MainActions
{
    public function __invoke()
    {
        $name   = !empty($_GET['name']) ? $_GET['name'] : null;
        
        if(!$name)
        {
            wp_die('Cron name is required!');
        }
        
        $method = str_replace('-','_',$name);
        
        if(method_exists($this,$method)){
            return $this->$method();
        }
        
        wp_die('Cron name '.$name.' is not valid!');
    }
}