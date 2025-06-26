<?php
/**
 * Fired during plugin activation.
 *
 * @package WPPB
 */

namespace WPPB\Core;

use WPPB\Traits\RequirementChecksTrait;
use function WPPB\plugin;

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

	use RequirementChecksTrait;

	/**
	 * The code that runs during plugin deinstallation.
	 */
	public static function run(): void {
		// Do something on uninstall.
	}
}
