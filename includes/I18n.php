<?php

namespace wppb;

// Exit if accessed directly
defined( 'WPINC' ) || die;

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 */
class I18n {


	/**
	 * Load the plugin text domain for translation.
	 * Text domain always needs to be passed as a string
	 * More info on link below
	 *
	 * @since    1.0.0
	 * @link ( https://developer.wordpress.org/themes/functionality/internationalization/#add-text-domain-to-strings, Add text domain to strings )
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'cs-wppb',
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages/'
		);

	}
}
