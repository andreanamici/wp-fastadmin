<?php

/**
 * Register here all plugin's shortcodes to use from wp pages and posts
 * 
 * Use in layout wp with: echo do_shortcode('[<shortcodename>]')
 */
return array(
    
   'fa_message' => array(
      'callable' => 'fa_message'
   )

   //  '<shortcode>' => array(
   //     'callable' => fa_action_callable('<object>, '<method>')
   //  ),
   
);