<?php

namespace wppb\admin;

use wppb\Assets;
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

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		// Load assets from manifest.json
		$this->assets = new Assets();
		add_action( 'admin_menu', array( __CLASS__, 'register_menu_page' ) );
		add_action( 'admin_menu', array( __CLASS__, 'register_submenu_page' ) );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( PLUGIN_NAME . '/wp/css', $this->assets->get( 'styles/admin.css' ), array(), PLUGIN_VERSION, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( PLUGIN_NAME . '/wp/js', $this->assets->get( 'scripts/admin.js' ), array(), PLUGIN_VERSION, false );
		wp_enqueue_script( PLUGIN_NAME . '/wp/js-clipboard', $this->assets->get( 'scripts/clipboard.js' ), array(), '2.0', true );
	}

	/**
	 * Register menu page.
	 * Only viewable by Administrators
	 *
	 * @since  1.0.1
	 * @action admin_menu
	 *
	 * @return void
	 */
	public static function register_menu_page() {

		add_menu_page(
			__( 'Plugin Settings', 'cs-wppb' ),
			__( 'Plugin Settings', 'cs-wppb' ),
			'manage_options',
			'cs-wppb',
			array( __CLASS__, 'render_options' ),
			'dashicons-admin-generic'
		);

	}

	/**
	 * Register submenu page and enqueue styles and scripts.
	 * Only viewable by Administrators
	 *
	 * @since  1.0.1
	 * @action admin_menu
	 *
	 * @return void
	 */
	public static function register_submenu_page() {

		add_submenu_page(
			'cs-wppb',
			__( 'System Status', 'cs-wppb' ),
			__( 'System Status', 'cs-wppb' ),
			'manage_options',
			'cs-wppb-system-status',
			array( __CLASS__, 'render_info' ),
		);
	}

	/**
	 * Render plugin system status
	 *
	 * @since  1.0.1
	 *
	 * @return void
	 */
	public static function render_info() {
		System_Report::system_report();
	}

	/**
	 * Render plugin options
	 *
	 * @since  1.0.1
	 *
	 * @return void
	 */
	public static function render_options() {
		System_Options::system_options();
	}

}
