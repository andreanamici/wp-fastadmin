<?php

namespace FastAdmin\lib\classes;


class FastAdminCron extends FastAdminCore
{    
    public $cronconfig;
    
    public function wp_init()
    {
        parent::wp_init();
        
        $this->cronconfig =  require_once WP_FA_BASE_PATH_CONFIGS . '/cron.php';
        
        $self = $this;
                        
        add_filter( 'cron_schedules', function($schedules) use($self){
                
                $schedules = array_merge($schedules,$self->cronconfig['schedules']);
                
                // Return our newly added schedule to be merged into the others
                return $schedules;

        }, 10, 1 );
        
        // Add function to register event to WordPress init
        add_action( 'init', array($this,'register_crontab'));
    }
    
    public function register_crontab()
    { 
//        
//          $cron_jobs = get_option( 'cron' );
//        fa_debug_print_r($cron_jobs);
//      
//      update_option('cron', '');

        foreach($this->cronconfig['crontab'] as $name => $cron)
        {           
            add_action($name, $cron['callable']);

            if(!wp_next_scheduled( $name ))
            {
                $res = wp_schedule_event( time(), $cron['interval'], $name);
            }
        }
    }   
}