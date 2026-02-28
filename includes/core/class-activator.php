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
	 */
	public static function activate(): void {
		$plugin = plugin();
		self::run_requirement_checks( $plugin->config );
	}
}
