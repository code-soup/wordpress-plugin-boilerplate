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
		$this->add_hooks();
	}

	/**
	 * Add the frontend hooks.
	 */
	private function add_hooks(): void {
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
		wp_enqueue_style(
			'wppb-common',
			plugin()->get( 'assets' )->get_asset_url( 'common.css' ),
			array(),
			plugin()->config['PLUGIN_VERSION']
		);

		wp_enqueue_script(
			'wppb-common',
			plugin()->get( 'assets' )->get_asset_url( 'common.js' ),
			array(),
			plugin()->config['PLUGIN_VERSION'],
			true
		);
	}
}
