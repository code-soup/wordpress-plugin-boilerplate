<?php

declare(strict_types=1);

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
	 * Plugin activation hook
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function activate(): void {
		// Use the lifecycle manager for activation
		$lifecycle = new Lifecycle();
		$lifecycle->activate();
	}
}
