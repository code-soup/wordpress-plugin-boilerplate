<?php

declare(strict_types=1);

namespace WPPB\Frontend;

use WPPB\Core\Init as CoreInit;
use WPPB\Core\Assets;
use WPPB\Traits\HelpersTrait;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * @file
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 */
class Init {


	use HelpersTrait;


	/**
	 * Main plugin instance
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

		// Main plugin instance
		$instance     = \WPPB\plugin_instance();
		$hooker       = $instance->get_hooker();
		$this->assets = $instance->get_assets();

		$hooker->add_actions(
			array(
				array( 'wp_enqueue_scripts', $this, 'enqueue_styles' ),
				array( 'wp_enqueue_scripts', $this, 'enqueue_scripts' ),
			)
		);
	}


	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * NOTE: Remember to enqueue your styles only on pages where needed
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_styles(): void {
		// Only load on frontend
		if ( is_admin() ) {
			return;
		}

		// Ensure assets are available
		if ( null === $this->assets ) {
			return;
		}

		wp_enqueue_style(
			$this->get_plugin_id( '/css' ),
			$this->assets->get( 'common.css' ),
			array(),
			$this->get_plugin_version(),
			'all'
		);
	}


	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * NOTE: Remember to enqueue your scripts only on templates where needed
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_scripts(): void {
		// Only load on frontend
		if ( is_admin() ) {
			return;
		}

		// Ensure assets are available
		if ( null === $this->assets ) {
			return;
		}

		$script_id = $this->get_plugin_id( '/js' );

		wp_enqueue_script(
			$script_id,
			$this->assets->get( 'common.js' ),
			array(),
			$this->get_plugin_version(),
			true // Load in footer for better performance
		);

		// Create nonce for AJAX security
		$nonce_action = $this->get_plugin_name() . '_frontend_nonce';

		wp_localize_script(
			$script_id,
			'wppb_frontend',
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
