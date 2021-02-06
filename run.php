<?php

// Autoload all classes via composer
$composer = require "vendor/autoload.php";

use wppb\PluginInit;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;


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
