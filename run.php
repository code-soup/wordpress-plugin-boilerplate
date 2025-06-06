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

use WPPB\Core\Init;
use WPPB\Core\Lifecycle;
use WPPB\Traits\HelpersTrait;

/**
 * The main plugin class
 */
final class WPPB_Plugin {

	use HelpersTrait;

	/**
	 * The single instance of the class
	 *
	 * @var WPPB_Plugin|null
	 */
	private static ?WPPB_Plugin $instance = null;

	/**
	 * The plugin's core functionality
	 *
	 * @var Init
	 */
	public Init $core;

	/**
	 * The plugin's lifecycle hooks
	 *
	 * @var Lifecycle
	 */
	public Lifecycle $lifecycle;

	/**
	 * The plugin's configuration
	 *
	 * @var array
	 */
	public array $config = array();

	/**
	 * Main Plugin instance
	 *
	 * @return WPPB_Plugin
	 */
	public static function instance(): WPPB_Plugin {
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
		$this->setup_config();

		$this->lifecycle = new Lifecycle( $this );
		$this->core      = new Init();

		$this->core->init();
	}

	/**
	 * Setup the plugin configuration.
	 */
	private function setup_config(): void {
		$this->config = array(
			'MIN_WP_VERSION_SUPPORT_TERMS' => '__MIN_WP_VERSION_SUPPORT_TERMS__',
			'MIN_WP_VERSION'               => '__MIN_WP_VERSION__',
			'MIN_PHP_VERSION'              => '__MIN_PHP_VERSION__',
			'MIN_MYSQL_VERSION'            => '__MIN_MYSQL_VERSION__',
			'PLUGIN_PREFIX'                => '__PLUGIN_PREFIX__',
			'PLUGIN_NAME'                  => '__PLUGIN_NAME__',
			'PLUGIN_VERSION'               => '__PLUGIN_VERSION__',
			'PLUGIN_TEXTDOMAIN'            => '__PLUGIN_TEXTDOMAIN__',
			'PLUGIN_BASE_PATH'             => __DIR__,
			'PLUGIN_URL'                   => plugin_dir_url( __FILE__ ),
			'PLUGIN_BASENAME'              => plugin_basename( __FILE__ ),
		);
	}
}

/**
 * Begins execution of the plugin.
 *
 * @return WPPB_Plugin
 */
function wppb_plugin(): WPPB_Plugin {
	return WPPB_Plugin::instance();
}

// Get the plugin running.
$wppb_plugin = wppb_plugin();
