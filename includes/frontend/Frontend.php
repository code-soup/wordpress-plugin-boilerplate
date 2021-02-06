<?php

namespace wppb\frontend;

use wppb\Utils;

// Exit if accessed directly
defined( 'WPINC' ) || die;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 */
class Frontend {

    use Utils;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
        // Do something if required
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
     *
     * NOTE: Remember to enqueue your styles only on templates where needed
     * 
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

        // Generate CSS handle
        $handle = $this->get_plugin_id() . '/css';

        wp_enqueue_style(
            $handle,
            $this->get_asset( 'styles/main.css' ),
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
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

        // Generate wp-admin Js handle
        $handle = $this->get_plugin_id() . '/js';

        wp_enqueue_script(
            $handle,
            $this->get_asset( 'scripts/main.js' ),
            array(),
            $this->get_plugin_version(),
            false
        );
	}

}
