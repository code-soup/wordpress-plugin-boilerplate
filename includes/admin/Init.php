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
		$hooker->add_actions( [
			[ 'admin_enqueue_scripts', $this ]
		] );
	}

	/**
	 * Enqueue the admin styles.
	 */
	public function admin_enqueue_scripts(): void {

		wp_enqueue_style(
			'wppb-admin-common',
			plugin()->get( 'assets' )->get_asset_url( 'admin-common.css' ),
			array( 'wppb-common' ),
			plugin()->config['PLUGIN_VERSION']
		);

		wp_enqueue_script(
			'wppb-admin-common',
			plugin()->get( 'assets' )->get_asset_url( 'admin-common.js' ),
			array( 'wppb-common' ),
			plugin()->config['PLUGIN_VERSION'],
			true
		);
	}
}
