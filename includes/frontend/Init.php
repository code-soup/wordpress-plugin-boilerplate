<?php
/**
 * Frontend Init Class.
 *
 * @package WPPB
 */

namespace WPPB\Frontend;

use function WPPB\plugin;

/**
 * If this file is called directly, abort.
 */
defined( 'ABSPATH' ) || die;

/**
 * The public-facing functionality of the plugin.
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
	 * Add the frontend hooks.
	 */
	private function add_hooks(): void {
		$hooker = plugin()->get( 'hooker' );

		$hooker->add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		$hooker->add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue the frontend styles.
	 */
	public function enqueue_styles(): void {
		wp_enqueue_style(
			plugin()->get_plugin_id( 'frontend' ),
			plugin()->get( 'assets' )->get( 'frontend.css' ),
			array(),
			plugin()->get_version()
		);
	}

	/**
	 * Enqueue the frontend scripts.
	 */
	public function enqueue_scripts(): void {
		wp_enqueue_script(
			plugin()->get_plugin_id( 'frontend' ),
			plugin()->get( 'assets' )->get( 'frontend.js' ),
			array(),
			plugin()->get_version(),
			true
		);
	}
}
