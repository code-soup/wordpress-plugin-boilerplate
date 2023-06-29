<?php

namespace WPPB;

// Exit if accessed directly
defined( 'WPINC' ) || die;

/**
 * @file
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 */
final class Init {

	/**
	 * Main plugin instance
	 */
	private static $instance;


	/**
	 * Define plugin constants
	 *
	 * @var array
	 */
	private $constants = array(
		'MIN_WP_VERSION_SUPPORT_TERMS' => '__PLUGIN_MIN_WP_VERSION__',
		'MIN_WP_VERSION'               => '__PLUGIN_MIN_WP_VERSION__',
		'MIN_PHP_VERSION'              => '__PLUGIN_MIN_PHP_VERSION__',
		'MIN_MYSQL_VERSION'            => '__PLUGIN_MIN_MYSQL_VERSION__',
		'PLUGIN_PREFIX'                => '__PLUGIN_PREFIX__',
		'PLUGIN_NAME'                  => '__PLUGIN_NAME__',
		'PLUGIN_VERSION'               => '__PLUGIN_VERSION__',
	);


	/**
	 * The hooker that's responsible for maintaining and registering all hooks that
	 * are loaded with the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Hooker    $hooker    Maintains and registers all hooks for the plugin.
	 */
	protected $hooker;


	/**
	 * The assets loader is responsible for all plugin assets, Js, CSS and images.
	 * Loads appropriate hashed files based on current environment
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Hooker    $hooker    Maintains and registers all hooks for the plugin.
	 */
	protected $assets;


	/**
	 * Make constructor protected, to prevent direct instantiation
	 *
	 * @since    1.0.0
	 */
	protected function __construct() {}


	/**
	 * Main Instance.
	 *
	 * Ensures only one instance is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 * @return Main instance.
	 */
	public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

	
	/**
     * Singletons should not be cloneable.
     */
    private function __clone()
    {
    	throw new \Exception('Cannot clone ' . __CLASS__);
    }

    
    /**
     * Singletons should not be restorable from strings.
     */
    private function __wakeup()
    {
        throw new \Exception('Cannot unserialize ' . __CLASS__);
    }



	/**
	 * Run everything on init
	 * @return void
	 */
	public function init() {

		// Define Constants.
		$this->set_constants();

		// Hooks loader.
		$this->hooker = new Hooker();

		// Assets loader.
		$this->assets = new Assets();

		// Internationalizations.
		new I18n();

		// WP Admin related stuff.
		new Admin\Init();

		// Public related stuff.
		new Frontend\Init();

		// Run all hooks
		$this->run();
	}


	/**
	 * Define Constants.
	 */
	private function set_constants() {

		$constants = $this->get_constants();

		foreach ( $constants as $define => $value )
		{
			if ( ! defined($define) )
			{
				define( $define, $value );
			}
		}
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

	/**
	 * The reference to the class that orchestrates the assets with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Hooker    Orchestrates the assets of the plugin.
	 */
	public function get_assets() {
		return $this->assets;
	}

	/**
	 * The reference to the class that orchestrates the assets with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Hooker    Orchestrates the assets of the plugin.
	 */
	public function get_constants() {
		return $this->constants;
	}
}
