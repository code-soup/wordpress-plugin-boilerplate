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
		$this->add_hooks();
	}

	/**
	 * Add the admin hooks.
	 */
	private function add_hooks(): void {
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
		$plugin_version = plugin()->config['PLUGIN_VERSION'];

		// Enqueue the main admin stylesheet.
		wp_enqueue_style(
			'__PLUGIN_PREFIX__-admin',
			$assets_handler->get_asset_url( 'admin-common.css' ),
			array(),
			$plugin_version
		);

		// Enqueue the webpack runtime script.
		wp_enqueue_script(
			'__PLUGIN_PREFIX__-runtime',
			$assets_handler->get_asset_url( 'runtime.js' ),
			array(),
			$plugin_version,
			true
		);

		// Enqueue the vendor libs script, dependent on the runtime.
		wp_enqueue_script(
			'__PLUGIN_PREFIX__-vendor',
			$assets_handler->get_asset_url( 'vendor-libs.js' ),
			array( '__PLUGIN_PREFIX__-runtime' ),
			$plugin_version,
			true
		);

		// Enqueue the main admin script, dependent on runtime and vendors.
		wp_enqueue_script(
			'__PLUGIN_PREFIX__-admin-common',
			$assets_handler->get_asset_url( 'admin-common.js' ),
			array( '__PLUGIN_PREFIX__-runtime', '__PLUGIN_PREFIX__-vendor' ),
			$plugin_version,
			true
		);
	}
}
