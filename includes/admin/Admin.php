<?php

namespace wppb\admin;

use wppb\Utils;

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
		// Add if something
	}

	/**
	 * Enqueue the stylesheets for wp-admin
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		// Generate wp-admin CSS handle
		wp_enqueue_style(
			$this->get_plugin_id( '/wp/css' ),
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

		wp_enqueue_script(
			$this->get_plugin_id( '/wp/js' ),
			$this->get_asset( 'scripts/admin.js' ),
			array(),
			$this->get_plugin_version(),
			false
		);
	}
}
