<?php

require_once 'vendor/autoload.php';

use FastAdmin\lib\classes\FastAdmin;

/**
 * Core constants
 */
define('WP_FA_PLUGIN_DIRNAME',basename(__DIR__));

define('WP_FA_BASE_PATH',dirname(__FILE__));

define('WP_FA_BASE_PATH_CLASSES',WP_FA_BASE_PATH.'/lib/classes');

define('WP_FA_BASE_PATH_ACTIONS',WP_FA_BASE_PATH.'/actions');

define('WP_FA_BASE_PATH_HELPERS',WP_FA_BASE_PATH.'/lib/helpers');

define('WP_FA_BASE_PATH_CONFIGS',WP_FA_BASE_PATH.'/configs');

define('WP_FA_BASE_PATH_RESOURCES',WP_FA_BASE_PATH.'/resources');

/**
 * FastAdmin 
 */
require_once WP_FA_BASE_PATH_CLASSES . '/FastAdmin.php';

global $fa;              /*@var $fa FastAdmin*/

$fa = new FastAdmin();
$fa->boot();

return $fa;