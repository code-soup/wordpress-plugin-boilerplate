<?php

namespace wppb;

use wppb\Hooker;
use wppb\I18n;
use wppb\admin\Admin;
use wppb\public\Public;

// Exit if accessed directly
defined( 'WPINC' ) || die;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 */
class PluginInit {

	use Utils;

	/**
	 * The hooker that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Hooker    $hooker    Maintains and registers all hooks for the plugin.
	 */
	protected $hooker;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		// Hooker Init
		$this->load_dependencies();

		// Set textdomain
		$this->set_locale();

		// Admin hooks
		$this->define_admin_hooks();

		// Public hooks
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Create an instance of the hooker which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		$this->hooker = new Hooker();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Ticket_Support_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$hooker     = $this->get_hooker();
		$class_i18n = new I18n();

		$hooker->add_action( 'plugins_loaded', $class_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$hooker      = $this->get_hooker();
		$class_admin = new Admin();

		$hooker->add_action( 'admin_enqueue_scripts', $class_admin, 'enqueue_styles' );
		$hooker->add_action( 'admin_enqueue_scripts', $class_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$hooker       = $this->get_hooker();
		$class_public = new Public();

		$hooker->add_action( 'wp_enqueue_scripts', $class_public, 'enqueue_styles' );
		$hooker->add_action( 'wp_enqueue_scripts', $class_public, 'enqueue_scripts' );

	}

	/**
	 * Run the hooker to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->hooker->run();
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Hooker    Orchestrates the hooks of the plugin.
	 */
	public function get_hooker() {
		return $this->hooker;
	}
}
