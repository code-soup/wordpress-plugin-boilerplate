<?php

// Autoload all classes via composer
require "vendor/autoload.php";

use wppb\Activator;
use wppb\Deactivator;
use wppb\PluginInit;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;


// Base plugin Path and URI
define( 'PLUGIN_URI', plugin_dir_url( __FILE__ ) );
define( 'PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

// Plugin details
define( 'PLUGIN_NAME', 'WPPB Plugin');
define( 'PLUGIN_VERSION', '1.0.0');
define( 'PLUGIN_TEXT_DOMAIN', 'wppb-plugin');


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/Activator.php
 */
register_activation_hook( __FILE__, ['wppb\Activator', 'activate']);


/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/Deactivator.php
 */
register_deactivation_hook( __FILE__, ['wppb\Deactivator', 'deactivate']);


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
$plugin = new PluginInit();
$plugin->run();
