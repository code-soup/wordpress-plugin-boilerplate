<?php

namespace WPPB;

// If this file is called directly, abort.
defined('WPINC') || die;

// Autoload all classes via composer.
require "vendor/autoload.php";

/**
 * Make main plugin class available via global function call.
 *
 * @since    1.0.0
 */
function plugin_instance() {

    return \WPPB\Init::get_instance();
}

// Init plugin
$plugin = plugin_instance();
$plugin->set_constants([
    'MIN_WP_VERSION_SUPPORT_TERMS' => '__PLUGIN_MIN_WP_VERSION__',
    'MIN_WP_VERSION'               => '__PLUGIN_MIN_WP_VERSION__',
    'MIN_PHP_VERSION'              => '__PLUGIN_MIN_PHP_VERSION__',
    'MIN_MYSQL_VERSION'            => '__PLUGIN_MIN_MYSQL_VERSION__',
    'PLUGIN_PREFIX'                => '__PLUGIN_PREFIX__',
    'PLUGIN_NAME'                  => '__PLUGIN_NAME__',
    'PLUGIN_VERSION'               => '__PLUGIN_VERSION__',
]);

$plugin->init();