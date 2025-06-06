<?php
/**
 * Plugin main file.
 *
 * @package WPPB
 */

namespace WPPB;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || die;

// Load composer autoloader for dependencies.
require 'vendor/autoload.php';

use WPPB\Core\Core;
use WPPB\Core\Deactivator;
use WPPB\Core\Lifecycle;

/**
 * The main plugin class
 */
final class Plugin {

	/**
	 * The single instance of the class
	 *
	 * @var Plugin|null
	 */
	private static ?Plugin $instance = null;

	/**
	 * The plugin's core functionality
	 *
	 * @var Core
	 */
	public Core $core;

	/**
	 * The plugin's lifecycle hooks
	 *
	 * @var Lifecycle
	 */
	public Lifecycle $lifecycle;

	/**
	 * Main Plugin instance
	 *
	 * @return Plugin
	 */
	public static function instance(): Plugin {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'WPPB' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'WPPB' ), '1.0.0' );
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		$this->core      = new Core();
		$this->lifecycle = new Lifecycle();

		$this->define_constants();

		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
	}

	/**
	 * Load the plugin text domain for translation.
	 */
	public function load_plugin_textdomain(): void {
		load_plugin_textdomain(
			'WPPB',
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages/'
		);
	}

	/**
	 * Deactivate the plugin.
	 */
	public function deactivate(): void {
		Deactivator::deactivate();
	}

	/**
	 * Define constants.
	 */
	private function define_constants(): void {
		$this->define( 'WPPB_VERSION', '1.0.0' );
		$this->define( 'WPPB_PATH', plugin_dir_path( __FILE__ ) );
		$this->define( 'WPPB_URL', plugin_dir_url( __FILE__ ) );
		$this->define( 'WPPB_BASENAME', plugin_basename( __FILE__ ) );
	}

	/**
	 * Define a constant if it's not already defined.
	 *
	 * @param string $name The constant name.
	 * @param mixed  $value The constant value.
	 */
	private function define( string $name, $value ): void {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}
}

/**
 * Begins execution of the plugin.
 *
 * @return Plugin
 */
function wppb_plugin(): Plugin {
	return Plugin::instance();
}

// Get the plugin running.
$wppb_plugin = wppb_plugin();

// Add a separate function for the uninstall hook.
register_uninstall_hook(
	__FILE__,
	function () {
		// Call the static uninstall method from the Lifecycle class.
		Lifecycle::uninstall();
	}
);
