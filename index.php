<?php
/**
 * Plugin main file.
 *
 * @package WPPB
 */

namespace WPPB;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || die;

/**
 * Plugin Name:       WordPress Plugin Boilerplate
 * Plugin URI:        https://github.com/code-soup/wordpress-plugin-boilerplate
 * Description:       A modern, modular WordPress plugin boilerplate with PSR-4 autoloading, Webpack 5 asset bundling, live reload, and code quality tools designed for streamlined, standards-compliant plugin development.
 * Version:           0.0.3
 * Requires at least: 6.0
 * Requires PHP:      8.1
 * Author:            Code Soup
 * Author URI:        https://codesoup.co
 * License:           GPL v3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Update URI:        https://github.com/code-soup/wordpress-plugin-boilerplate
 * Text Domain:       wppb-textdomain
 * Domain Path:       /languages
 */

// Load autoloaders BEFORE registering hooks.
require_once __DIR__ . '/vendor/autoload.php';
\WPPB\Autoloader::register( __DIR__ );

// NOTE: Activation hooks need to be inside index.php file or it might not work properly.
// It can fail without error, WordPress is silently failing in case of error.

// The code that runs during plugin activation.
register_activation_hook(
	__FILE__,
	array( \WPPB\Core\Activator::class, 'activate' )
);

// The code that runs during plugin deactivation.
register_deactivation_hook(
	__FILE__,
	array( \WPPB\Core\Deactivator::class, 'deactivate' )
);

// Run plugin, run.
require_once __DIR__ . '/run.php';
