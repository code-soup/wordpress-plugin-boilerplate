<?php
/**
 * Fired during plugin activation.
 *
 * @package WPPB
 */

declare( strict_types=1 );

namespace WPPB\Core;

// Exit if accessed directly.
defined( 'ABSPATH' ) || die;

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 */
class Activator {

	/**
	 * The code that runs during plugin activation.
	 */
	public static function activate(): void {
		// Trigger module activation hooks
		do_action( '__PLUGIN_PREFIX___activate' );

		// Uncomment when the plugin registers rewrite rules (custom post types, endpoints, etc.).
		// flush_rewrite_rules();
	}
}
