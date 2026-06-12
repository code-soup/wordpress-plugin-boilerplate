<?php
/**
 * Fired during plugin activation.
 *
 * @package WPPB
 */

namespace WPPB\Core;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

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
		do_action( '__PLUGIN_NAME___activate' );

		// Flush rewrite rules after all CPTs are registered
		flush_rewrite_rules();
	}
}
