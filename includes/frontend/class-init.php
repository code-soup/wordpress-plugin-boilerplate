<?php
/**
 * Frontend Init Class.
 *
 * @package WPPB
 */

namespace WPPB\Frontend;

use function WPPB\plugin;

/** If this file is called directly, abort. */
defined( 'ABSPATH' ) || die;

/**
 * The public-facing functionality of the plugin.
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
				array( 'wp_enqueue_scripts', $this ),
			)
		);
	}

	/**
	 * Enqueue the frontend styles.
	 */
	public function wp_enqueue_scripts(): void {

		$assets_handler = plugin()->get( 'assets' );
		$plugin_version = plugin()->config['PLUGIN_VERSION'];

		// Build dependencies list, only including assets that exist.
		$dependencies = array();

		// Check and enqueue the webpack runtime script.
		if ( $assets_handler->asset_exists( 'runtime.js' ) ) {
			wp_enqueue_script(
				'wppb-frontend-runtime',
				$assets_handler->get_asset_url( 'runtime.js' ),
				array(),
				$plugin_version,
				true
			);
			$dependencies[] = 'wppb-frontend-runtime';
		}

		// Check and enqueue the vendor libs script.
		if ( $assets_handler->asset_exists( 'vendor-libs.js' ) ) {
			wp_enqueue_script(
				'wppb-frontend-vendor',
				$assets_handler->get_asset_url( 'vendor-libs.js' ),
				$dependencies,
				$plugin_version,
				true
			);
			$dependencies[] = 'wppb-frontend-vendor';
		}

		// Check and enqueue the main frontend stylesheet.
		if ( $assets_handler->asset_exists( 'frontend-common.css' ) ) {
			wp_enqueue_style(
				'wppb-frontend',
				$assets_handler->get_asset_url( 'frontend-common.css' ),
				array(),
				$plugin_version
			);
		}

		// Check and enqueue the main frontend script.
		if ( $assets_handler->asset_exists( 'frontend-common.js' ) ) {
			wp_enqueue_script(
				'wppb-frontend-common',
				$assets_handler->get_asset_url( 'frontend-common.js' ),
				$dependencies,
				$plugin_version,
				true
			);
		}
	}
}
