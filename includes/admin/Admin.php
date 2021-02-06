<?php

namespace wppb\admin;

use wppb\Utils;
use wppb\status\System_Report;
use wppb\status\System_Options;

// Exit if accessed directly
defined( 'WPINC' ) || die;


/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 */
class Admin {

	use Utils;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		add_action( 'admin_menu', array( $this, 'register_menu_page' ) );
	}

	/**
	 * Enqueue the stylesheets for wp-admin
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		// Generate wp-admin CSS handle
		$handle = $this->get_plugin_id() . '/wp/css';

		wp_enqueue_style(
			$handle,
			$this->get_asset( 'styles/admin.css' ),
			array(),
			$this->get_plugin_version(),
			'all'
		);
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		// Generate wp-admin Js handle
		$handle = $this->get_plugin_id() . '/wp/js';

		wp_enqueue_script(
			$handle,
			$this->get_asset( 'scripts/admin.js' ),
			array(),
			$this->get_plugin_version(),
			false
		);
	}

	/**
	 * Register menu and submenu page
	 * Only accessible for user with manage_options capability
	 *
	 * @since  1.0.1
	 * @action admin_menu
	 *
	 * @return void
	 */
	public function register_menu_page() {

		// Register menu page
		$page_id = $this->get_plugin_id();

		// Menu item
		add_menu_page(
			__( 'Plugin Settings', 'cs-wppb' ),
			__( 'Plugin Settings', 'cs-wppb' ),
			'manage_options',
			$page_id,
			array( $this, 'render_options' ),
			'dashicons-admin-generic'
		);

		// Submenu item
		add_submenu_page(
			$page_id,
			__( 'System Status', 'cs-wppb' ),
			__( 'System Status', 'cs-wppb' ),
			'manage_options',
			$page_id . '-system-status',
			array( $this, 'render_info' ),
		);
	}


	/**
	 * Render plugin system status
	 *
	 * @since  1.0.1
	 *
	 * @return void
	 */
	public function render_info() {
		// System_Report::system_report();
	}

	/**
	 * Render plugin options
	 *
	 * @since  1.0.1
	 *
	 * @return void
	 */
	public function render_options() {
		// System_Options::system_options();
	}

}
