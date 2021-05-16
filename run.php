<?php

// If this file is called directly, abort.
defined('WPINC') || die;

// Autoload all classes via composer.
require "autoloader.php";

/**
 * Make main plugin class available via global function call.
 *
 * @since    1.0.0
 */
function wppb() {

    return WPPB\Init::get_instance();
}

// Init plugin and make instance globally available
$instance = wppb();
$instance->run();

$GLOBALS['wppb'] = $instance;