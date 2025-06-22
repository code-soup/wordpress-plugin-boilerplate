<?php
/**
 * Plugin main file.
 *
 * @package WPPB
 */

namespace WPPB;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || die;

// Load composer autoloader for dependencies.
require 'vendor/autoload.php';

use WPPB\Core\Plugin;

/**
 * Begins execution of the plugin.
 *
 * @return Plugin
 */
function plugin(): Plugin {
	static $instance = null;

	if ( is_null( $instance ) ) {
		$config = array(
			'MIN_WP_VERSION_SUPPORT_TERMS' => '__PLUGIN_MIN_WP_VERSION__',
			'MIN_WP_VERSION'               => '__PLUGIN_MIN_WP_VERSION__',
			'MIN_PHP_VERSION'              => '__PLUGIN_MIN_PHP_VERSION__',
			'MIN_MYSQL_VERSION'            => '__PLUGIN_MIN_MYSQL_VERSION__',
			'PLUGIN_PREFIX'                => '__PLUGIN_PREFIX__',
			'PLUGIN_NAME'                  => '__PLUGIN_NAME__',
			'PLUGIN_VERSION'               => '__PLUGIN_VERSION__',
			'PLUGIN_TEXTDOMAIN'            => '__PLUGIN_TEXTDOMAIN__',
			'ENVIRONMENT'                  => wp_get_environment_type(),
		);

		// Pass the main plugin file path and config to the instance method.
		$instance = Plugin::instance( __FILE__, $config );
	}

	return $instance;
}

// Get the plugin running.
plugin();
