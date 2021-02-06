<?php

namespace wppb;

use wppb\Loader;
use wppb\I18n;
use wppb\admin\Admin;
use wppb\frontend\Frontend;

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
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

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

		// Loader Init
		$this->load_dependencies();

		// Set textdomain
		$this->set_locale();

		// Admin hooks
		$this->define_admin_hooks();

		// Frontend hooks
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		$this->loader = new Loader();

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

		$loader     = $this->get_loader();
		$class_i18n = new I18n();

		$loader->add_action( 'plugins_loaded', $class_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$loader      = $this->get_loader();
		$class_admin = new Admin();

		$loader->add_action( 'admin_enqueue_scripts', $class_admin, 'enqueue_styles' );
		$loader->add_action( 'admin_enqueue_scripts', $class_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$loader       = $this->get_loader();
		$class_public = new Frontend();

		$loader->add_action( 'wp_enqueue_scripts', $class_public, 'enqueue_styles' );
		$loader->add_action( 'wp_enqueue_scripts', $class_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Ticket_Support_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}
}
