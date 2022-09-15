<?php

/**
 * Configure here wp filters
 */
return array(
    
    'message' => array(
        'name'     => 'the_content',
        'callable' => function( $content ) {
                if($content && stristr('[fa_message]',$content) === false)
                {
                    $content = fa_message_get(). ' '.$content;
                }
                return $content ;
        }
    )
);