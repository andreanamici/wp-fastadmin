<?php

return array(
    
    /**
     * Define interval here
     */
    'schedules' => array(
        'ten_sec'         => array('interval'  => 10,  'display' => 'Once Every 10 Seconds' ),
        'one_minute'      => array('interval'  => 60,  'display' => 'Once Every 1 Minute' ),
        'ten_minutes'     => array('interval'  => 600, 'display' => 'Once Every 10 Minutes'),
        'fifteen_minutes' => array('interval'  => 900, 'display' => 'Once Every 15 Minutes'),
        'thirty_minutes'  => array('interval'  => 1800,'display' => 'Once Every 30 Minute'),
        'weekly'          => array('interval'  => 604800,'display' => 'Once Weekly'),
        'montly'          => array('interval'  => 2635200,'display' => 'Once a Month'),
    ),
    
    /**
     * Define cron operation here, es: <cron> => [ <interval> => '...', <callable> => action to execute ]
     */
    'crontab' => array(
  
        // '<cronname>' => array(
        //     'interval'     => '<interval>',
        //     'callable'     => fa_action_callable('FastAdmin\actions\Cron', '<method>')
        // ),
    )
    
);