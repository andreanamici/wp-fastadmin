<?php
/*
Plugin Name: FastAdmin
Plugin URI: https://github.com/andreanamici/wp-fastadmin
Description: Questo è il plugin che gestisce amministrazioni personalizzate
Author: Andrea Namici
Version: 2
Author URI: http://www.andreanamici.it
*/

$fa = require_once dirname(__FILE__).'/bootstrap.php';/*@var $fa FastAdmin*/

$fa->start();