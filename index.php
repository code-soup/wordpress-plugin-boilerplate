<?php // Silence is golden

/**
 * The plugin bootstrap file
 *
 *
 * @link              https://www.codesoup.co
 * @since             1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       WordPress Plugin Boilerplate
 * Plugin URI:        https://github.com/code-soup/wordpress-plugin-boilerplate
 * Description:       WordPress Plugin Boilerplate with webpack build script and php namespacing
 * Version:           1.0.0
 * Author:            Code Soup, brbs, Kodelato
 * Author URI:        https://www.bobz.co, https://brbs.works/, https://kodelato.hr/
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       cs-wppb
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

// Run the plugin
include "run.php";