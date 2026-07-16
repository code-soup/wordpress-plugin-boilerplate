<?php
/**
 * Fired during plugin deactivation.
 *
 * @package WPPB
 */

namespace WPPB\Core;

/**
 * If this file is called directly, abort.
 */
defined( 'ABSPATH' ) || die;

/**
 * Deactivator class.
 */
class Deactivator {

	/**
	 * Deactivate the plugin.
	 */
	public static function deactivate(): void {
		// Trigger module activation hooks
		do_action( '__PLUGIN_NAME___deactivate' );

		// Flush rewrite rules after all CPTs are registered
		flush_rewrite_rules();
	}
}
