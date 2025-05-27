<?php

namespace WPPB\Admin;

// Exit if accessed directly
defined( 'WPINC' ) || die;

/**
 * @file
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 */
class Init {

	use \WPPB\Traits\HelpersTrait;

	/**
	 * Main plugin instance.
	 * 
	 * @var \WPPB\Init|null
	 * @since 1.0.0
	 */
	protected static $instance = null;

	/**
	 * Assets loader class.
	 * 
	 * @var \WPPB\Assets
	 * @since 1.0.0
	 */
	protected $assets;

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
	 * @since    1.0.0
	 * @return void
	 */
	public function admin_enqueue_scripts(): void {

		wp_enqueue_style(
			$this->get_plugin_id('/wp/css'),
			$this->assets->get('admin.css'),
			array(),
			$this->get_plugin_version(),
			'all'
		);

		$script_id = $this->get_plugin_id('/wp/js');

		wp_enqueue_script(
			$script_id,
			$this->assets->get('admin.js'),
			array(),
			$this->get_plugin_version(),
			false
		);

		wp_localize_script(
            $script_id,
            'wppb',
            array(
                'nonce'    => wp_create_nonce( 'wppb_wp_xhr_nonce' ),
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'post_id'  => get_the_ID(),
            )
        );
	}
}
