<?php
/**
 * Fired during plugin deactivation.
 *
 * @package WPPB
 */

declare( strict_types=1 );

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
		do_action( '__PLUGIN_PREFIX___deactivate' );

		// Uncomment when the plugin registers rewrite rules (custom post types, endpoints, etc.).
		// flush_rewrite_rules();
	}
}
