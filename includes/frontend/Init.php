<?php
/**
 * Frontend Init Class.
 *
 * @package WPPB
 */

namespace WPPB\Frontend;

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
		$hooker = wppb_plugin()->get( 'hooker' );

		$hooker->add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		$hooker->add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue the frontend styles.
	 */
	public function enqueue_styles(): void {
		wp_enqueue_style(
			wppb_plugin()->get_plugin_id( 'frontend' ),
			wppb_plugin()->get( 'assets' )->get( 'frontend.css' ),
			array(),
			wppb_plugin()->get_version()
		);
	}

	/**
	 * Enqueue the frontend scripts.
	 */
	public function enqueue_scripts(): void {
		wp_enqueue_script(
			wppb_plugin()->get_plugin_id( 'frontend' ),
			wppb_plugin()->get( 'assets' )->get( 'frontend.js' ),
			array(),
			wppb_plugin()->get_version(),
			true
		);
	}
}
