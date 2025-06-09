<?php
/**
 * Fired during plugin activation.
 *
 * @package WPPB
 */

namespace WPPB\Core;

use WPPB\Traits\RequirementChecksTrait;

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

	use RequirementChecksTrait;

	/**
	 * The code that runs during plugin activation.
	 *
	 * @param Plugin $plugin The main plugin instance.
	 */
	public static function activate( Plugin $plugin ): void {
		self::run_requirement_checks( $plugin->config );
	}
}
