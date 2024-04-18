<?php

namespace WPPB\Frontend;

// Exit if accessed directly
defined( 'WPINC' ) || die;

/**
 * @file
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 */
class Init {


    use \WPPB\Traits\HelpersTrait;


    // Main plugin instance
    protected static $instance = null;


    // Assets loader class.
    protected $assets;


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

        $hooker->add_actions([
            ['wp_enqueue_scripts', $this, 'enqueue_styles'],
            ['wp_enqueue_scripts', $this, 'enqueue_scripts']
        ]);
    }


    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * NOTE: Remember to enqueue your styles only on pages where needed
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            $this->get_plugin_id('/css'),
            $this->assets->get('common.css'),
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

        $script_id = $this->get_plugin_id('/js');

        wp_enqueue_script(
            $script_id,
            $this->assets->get('common.js'),
            array(),
            $this->get_plugin_version(),
            false
        );

        wp_localize_script(
            $script_id,
            'wppb',
            array(
                'nonce'    => wp_create_nonce( 'wppb_xhr_nonce' ),
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'post_id'  => get_the_ID(),
            )
        );
    }
}
