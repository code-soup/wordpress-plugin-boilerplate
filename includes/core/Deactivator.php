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
		// Flush if needed only.
	}
}
