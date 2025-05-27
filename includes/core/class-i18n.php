<?php

declare(strict_types=1);

namespace WPPB\Core;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * @file
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 */
class I18n {

	use \WPPB\Traits\HelpersTrait;

	/**
	 * Main plugin instance.
	 *
	 * @var self|null
	 * @since 1.0.0
	 */
	protected static ?self $instance = null;

	/**
	 * Initialize internationalization
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->register_hooks();
	}

	/**
	 * Register hooks for internationalization
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function register_hooks(): void {
		// Get the main plugin instance and hooker
		$instance = \WPPB\plugin_instance();
		$hooker   = $instance->get_hooker();

		// Register the textdomain loading hook
		$hooker->add_action( 'init', $this, 'load_textdomain' );
	}

	/**
	 * Load the plugin text domain for translation.
	 * Text domain always needs to be passed as a string
	 * More info on link below
	 *
	 * @since 1.0.0
	 * @link https://developer.wordpress.org/themes/functionality/internationalization/#add-text-domain-to-strings
	 * @return void
	 */
	public function load_textdomain(): void {
		$plugin_name = $this->get_plugin_name();
		$text_domain = strtolower( str_replace( '_', '-', $plugin_name ) );

		$languages_path = $this->get_plugin_dir_path( '/languages' );
		$relative_path  = plugin_basename( $languages_path );

		load_plugin_textdomain(
			$text_domain,
			false,
			$relative_path
		);
	}
}
