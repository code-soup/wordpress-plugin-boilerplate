<?php
/**
 * Admin Init class.
 *
 * @package WPPB
 */

declare( strict_types=1 );

namespace WPPB\Admin;

use function WPPB\plugin;

/** If this file is called directly, abort. */
defined( 'ABSPATH' ) || die;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 */
class Init {

	/**
	 * Init constructor.
	 */
	public function __construct() {
		// Hooks registered later to avoid circular dependency
	}

	/**
	 * Initialize and register hooks.
	 */
	public function init(): void {
		$hooker = plugin()->get( 'hooker' );
		$hooker->add_actions(
			array(
				array( 'admin_enqueue_scripts', $this ),
			)
		);
	}

	/**
	 * Enqueue the admin styles.
	 */
	public function admin_enqueue_scripts(): void {

		$assets_handler = plugin()->get( 'assets' );
		$plugin_version = plugin()->get_config( 'PLUGIN_VERSION' );

		// Build dependencies list, only including assets that exist.
		$dependencies = array();

		// Check and enqueue the webpack runtime script.
		if ( $assets_handler->asset_exists( 'runtime.js' ) ) {
			wp_enqueue_script(
				'wppb-admin-runtime',
				$assets_handler->get_asset_url( 'runtime.js' ),
				array(),
				$plugin_version,
				true
			);
			$dependencies[] = 'wppb-admin-runtime';
		}

		// Check and enqueue the vendor libs script.
		if ( $assets_handler->asset_exists( 'vendor-libs.js' ) ) {
			wp_enqueue_script(
				'wppb-admin-vendor',
				$assets_handler->get_asset_url( 'vendor-libs.js' ),
				$dependencies,
				$plugin_version,
				true
			);
			$dependencies[] = 'wppb-admin-vendor';
		}

		// Check and enqueue the main admin stylesheet.
		if ( $assets_handler->asset_exists( 'admin-common.css' ) ) {
			wp_enqueue_style(
				'wppb-admin',
				$assets_handler->get_asset_url( 'admin-common.css' ),
				array(),
				$plugin_version
			);
		}

		// Check and enqueue the main admin script.
		if ( $assets_handler->asset_exists( 'admin-common.js' ) ) {
			wp_enqueue_script(
				'wppb-admin-common',
				$assets_handler->get_asset_url( 'admin-common.js' ),
				$dependencies,
				$plugin_version,
				true
			);
		}
	}
}
