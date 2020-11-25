    <?php

/**
 * Define here wp pages actions
 * 
 * When plugin is running, you can use index.php?page=<action> and register a Controller and method 
 * 
 * if slug is of a valid wp page/post, it will be ignored.
 */
return array(   

    'cron' => array(
        'page_title'    => null,
        'capability'    => null,
        'global'        => true,
        'callable'      => fa_action_callable('FastAdmin\actions\Cron')
    ),

    // '<slug>' => array(
    //     'page_title'    => null,
    //     'capability'    => null,
    //     'global'        => true,
    //     'callable'      => fa_action_callable('<class>', '<method>')
    // ),
);