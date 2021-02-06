<?php // Silence is golden

/**
 * The plugin bootstrap file
 *
 * @author            Code Soup
 * @copyright         2021 Code Soup
 * @license           GPL-3.0+
 *
 * @link              https://www.codesoup.co
 * @since             1.0.0
 * 
 * @wordpress-plugin
 * Plugin Name:       WordPress Plugin Boilerplate
 * Plugin URI:        https://github.com/code-soup/wordpress-plugin-boilerplate
 * Description:       WordPress Plugin Boilerplate with webpack build script, composer autoloader and php namespacing
 * Version:           1.0.0
 * Author:            Code Soup
 * Author URI:        https://www.codesoup.co
 * Contributors:      bobz, brbs, Kodelato
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       cs-wppb
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Check for PHP version and include file if everything is ok

$min_php_ver = strtoupper( $constants_prefix . $constants_define['MIN_PHP_VERSION'] );

if ( version_compare( PHP_VERSION, $min_php_ver, '<' ) )
{
    // Current PHP version is lower than required
    add_action('admin_notices', function () {

        $message = sprintf(
            __('Minimum PHP version required to run this plugin is %s. Your current version is %s', 'cs-wppb'),
            $min_php_ver,
            PHP_VERSION
        );

        echo sprintf(
            '<div class="notice notice-warning is-dismissible"><p>%s</p></div>',
            $message
        );
    });

    // Don't go further
    return;
}


/**
 * NOTE: Needs to be inside index.php file or it might not work properly
 * It can fail without error, WordPress is not sending back reason for error
 *
 * 
 * The code that runs during plugin activation.
 * - includes/Activator.php
 */
register_activation_hook( __FILE__, function() {

    // On activate do this
    wppb\Activator::activate();
});



/**
 * The code that runs during plugin deactivation.
 * - includes/Deactivator.php
 */
register_deactivation_hook( __FILE__, function () {
    
    // On deactivate do that
    wppb\Deactivator::deactivate();
});


// Run plugin, run
include "run.php";