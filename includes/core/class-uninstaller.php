<?php
/**
 * Fired during plugin uninstall.
 *
 * @package WPPB
 */

namespace WPPB\Core;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Fired during plugin deinstallation.
 *
 * This class defines all code necessary to run during the plugin's deinstallation.
 *
 * @since      2.0.2
 */
class Uninstaller {

	/**
	 * The code that runs during plugin deinstallation.
	 */
	public static function run(): void {
		// Do something on uninstall.
	}
}
