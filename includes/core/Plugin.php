<?php
/**
 * Main Plugin class.
 *
 * @package WPPB
 */

declare(strict_types=1);

namespace WPPB\Core;

use WPPB\Traits\HelpersTrait;

/**
 * The main plugin class
 */
final class Plugin {

	use HelpersTrait;

	/**
	 * The single instance of the class
	 *
	 * @var Plugin|null
	 */
	private static ?Plugin $instance = null;

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
	 * The dependency injection container.
	 *
	 * @var Container
	 */
	public Container $container;

	/**
	 * The main plugin file path.
	 *
	 * @var string
	 */
	private string $plugin_file;

	/**
	 * Main Plugin instance
	 *
	 * @param string               $plugin_file Main plugin file path.
	 * @param array<string, mixed> $config The plugin configuration.
	 * @return Plugin
	 */
	public static function instance( string $plugin_file, array $config ): Plugin {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self( $plugin_file, $config );
		}

		return self::$instance;
	}

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', '__PLUGIN_TEXTDOMAIN__' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', '__PLUGIN_TEXTDOMAIN__' ), '1.0.0' );
	}

	/**
	 * Constructor
	 *
	 * @param string               $plugin_file Main plugin file path.
	 * @param array<string, mixed> $config The plugin configuration.
	 */
	private function __construct( string $plugin_file, array $config ) {
		$this->plugin_file = $plugin_file;
		$this->setup_config( $config );

		$this->container = new Container();
		$this->register_services();

		$this->core = new Init( $this->container );
		$this->core->init();
	}

	/**
	 * Register the essential services for the plugin.
	 */
	private function register_services(): void {
		$this->container->singleton(
			'hooker',
			fn() => new \WPPB\Core\Hooker()
		);

		$this->container->singleton(
			'lifecycle',
			fn() => new \WPPB\Core\Lifecycle( $this )
		);

		// Register Assets and I18n here, where they belong.
		$this->container->singleton( 'assets', \WPPB\Core\Assets::class );
		$this->container->singleton( 'i18n', \WPPB\Core\I18n::class );
	}

	/**
	 * Setup the plugin configuration.
	 *
	 * @param array<string, mixed> $config The plugin configuration.
	 */
	private function setup_config( array $config ): void {
		$this->config = array_merge(
			$config,
			array(
				'PLUGIN_BASE_PATH' => plugin_dir_path( $this->plugin_file ),
				'PLUGIN_URL'       => plugin_dir_url( $this->plugin_file ),
				'PLUGIN_BASENAME'  => plugin_basename( $this->plugin_file ),
			)
		);
	}
}
