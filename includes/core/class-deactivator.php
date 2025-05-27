<?php

declare(strict_types=1);

namespace WPPB\Core;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


/**
 * @file
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 */
class Deactivator {

	/**
	 * Plugin deactivation hook
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function deactivate(): void {
		// Use the lifecycle manager for deactivation
		$lifecycle = new Lifecycle();
		$lifecycle->deactivate();
	}
}
