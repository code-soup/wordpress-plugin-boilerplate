<?php
/**
 * Admin Init class.
 *
 * @package WPPB
 */

declare(strict_types=1);

namespace WPPB\Admin;

/**
 * If this file is called directly, abort.
 */
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
		$hooker = wppb_plugin()->get( 'hooker' );

		$hooker->add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		$hooker->add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue the admin styles.
	 */
	public function enqueue_styles(): void {
		wp_enqueue_style(
			wppb_plugin()->get_plugin_id( 'admin' ),
			wppb_plugin()->get( 'assets' )->get( 'admin.css' ),
			array(),
			wppb_plugin()->get_version()
		);
	}

	/**
	 * Enqueue the admin scripts.
	 */
	public function enqueue_scripts(): void {
		wp_enqueue_script(
			wppb_plugin()->get_plugin_id( 'admin' ),
			wppb_plugin()->get( 'assets' )->get( 'admin.js' ),
			array(),
			wppb_plugin()->get_version(),
			true
		);
	}
}
