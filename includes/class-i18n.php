<?php

namespace WPPB;

// Exit if accessed directly
defined( 'WPINC' ) || die;

/**
 * @file
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 */
class I18n {

	// Main plugin instance.
	protected static $instance = null;

	public function __construct() {
		
		// Main plugin instance
		$instance = wppb();
		$hooker   = $instance->get_hooker();

		$hooker->add_action('init', $this, 'load_textdomain');
	}



	/**
	 * Load the plugin text domain for translation.
	 * Text domain always needs to be passed as a string
	 * More info on link below
	 *
	 * @since    1.0.0
	 * @link ( https://developer.wordpress.org/themes/functionality/internationalization/#add-text-domain-to-strings, Add text domain to strings )
	 */
	public function load_textdomain() {

		$root = untrailingslashit( dirname( plugin_basename(__FILE__), 2) );

		load_plugin_textdomain(
			'cs-wppb',
			false,
			$root . '/languages/'
		);
	}
}
