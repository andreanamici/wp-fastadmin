<?php

namespace FastAdmin\lib\classes;

/**
 * Core class of Perfect Survey Plugin
 */
#[\AllowDynamicProperties]
abstract class FastAdminCore
{
    public function wp_init()
    {
        return $this;
    }
}
