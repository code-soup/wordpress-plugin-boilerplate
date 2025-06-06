<?php
/**
 * I18n class.
 *
 * @package WPPB
 */

declare(strict_types=1);

namespace WPPB\Core;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
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
	 * I18n constructor.
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
		// Get the main plugin instance and hooker.
		$instance = \WPPB\wppb_plugin();
		$hooker   = $instance->get( 'hooker' );

		// Register the textdomain loading hook.
		$hooker->add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
	}

	/**
	 * Load the plugin text domain.
	 */
	public function load_plugin_textdomain(): void {
		load_plugin_textdomain(
			'__PLUGIN_TEXTDOMAIN__',
			false,
			wppb_plugin()->get_basename() . '/languages/'
		);
	}
}
