<?php

// Autoload all classes via composer
$composer = require "vendor/autoload.php";

use wppb\PluginInit;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

// Base plugin Path and URI
define( 'CS_WPPB_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
define( 'CS_WPPB_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

// Plugin details
define( 'CS_WPPB_PLUGIN_NAME', 'WordPress Plugin Boilerplate');
define( 'CS_WPPB_PLUGIN_VERSION', '1.0.0');

/* echo '<pre>';
print_r( $composer );
echo '</pre>'; */

/**
 * Load plugin
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
$plugin = new PluginInit();
$plugin->run();
