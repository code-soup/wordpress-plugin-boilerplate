<?php

declare(strict_types=1);

namespace WPPB\Admin;

use WPPB\Core\Init as CoreInit;
use WPPB\Core\Assets;
use WPPB\Traits\HelpersTrait;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * @file
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 */
class Init {

	use HelpersTrait;

		/**
	 * Main plugin instance.
	 *
	 * @var CoreInit|null
	 * @since 1.0.0
	 */
	protected static ?CoreInit $instance = null;

	/**
	 * Assets loader class.
	 *
	 * @var Assets|null
	 * @since 1.0.0
	 */
	protected ?Assets $assets = null;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		// Main plugin instance.
		$instance     = \WPPB\plugin_instance();
		$hooker       = $instance->get_hooker();
		$this->assets = $instance->get_assets();

		// Admin hooks.
		$hooker->add_action( 'admin_enqueue_scripts', $this );
	}

	/**
	 * Register the CSS/JavaScript for the admin area.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function admin_enqueue_scripts(): void {
		// Security check - only load on admin pages
		if ( ! is_admin() ) {
			return;
		}

		// Capability check - only for users who can manage options
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Ensure assets are available
		if ( null === $this->assets ) {
			return;
		}

		wp_enqueue_style(
			$this->get_plugin_id( '/wp/css' ),
			$this->assets->get( 'admin.css' ),
			array(),
			$this->get_plugin_version(),
			'all'
		);

		$script_id = $this->get_plugin_id( '/wp/js' );

		wp_enqueue_script(
			$script_id,
			$this->assets->get( 'admin.js' ),
			array(),
			$this->get_plugin_version(),
			true // Load in footer for better performance
		);

		// Create nonce for AJAX security
		$nonce_action = $this->get_plugin_name() . '_admin_nonce';

		wp_localize_script(
			$script_id,
			'wppb_admin',
			array(
				'nonce'      => wp_create_nonce( $nonce_action ),
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'post_id'    => get_the_ID(),
				'rest_url'   => rest_url(),
				'rest_nonce' => wp_create_nonce( 'wp_rest' ),
			)
		);
	}
}
