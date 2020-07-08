<?php

namespace wppb\frontend;

use wppb\Assets;

// Exit if accessed directly
defined( 'WPINC' ) || die;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 */
class Frontend {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
        
        // Load assets from manifest.json
        $this->assets = new Assets();
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
     *
     * NOTE: Remember to enqueue your styles only on templates where needed
     * 
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( PLUGIN_NAME . '/css', $this->assets->get('styles/main.css'), [], PLUGIN_VERSION, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
     * 
	 * NOTE: Remember to enqueue your scripts only on templates where needed
     * 
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( PLUGIN_NAME . '/js', $this->assets->get('scripts/main.js'), [], PLUGIN_VERSION, false );
	}

}
