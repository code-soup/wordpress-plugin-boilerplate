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
	 * The plugin's text domain.
	 *
	 * @var string
	 */
	protected string $domain;

	/**
	 * I18n constructor.
	 *
	 * @param string $domain The plugin's text domain.
	 */
	public function __construct( string $domain ) {
		$this->domain = $domain;
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
		$instance = \WPPB\plugin_instance();
		$hooker   = $instance->get_hooker();

		// Register the textdomain loading hook.
		$hooker->add_action( 'init', $this, 'load_plugin_textdomain' );
	}

	/**
	 * Get the text domain.
	 *
	 * @return string
	 */
	public function get_domain(): string {
		return $this->domain;
	}

	/**
	 * Set the text domain.
	 *
	 * @param string $domain The text domain.
	 */
	public function set_domain( string $domain ): void {
		$this->domain = $domain;
	}

	/**
	 * Load the plugin text domain.
	 */
	public function load_plugin_textdomain(): void {
		load_plugin_textdomain(
			$this->domain,
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
}
