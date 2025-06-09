<?php
/**
 * Main Plugin class.
 *
 * @package WPPB
 */

declare(strict_types=1);

namespace WPPB\Core;

use WPPB\Providers\AdminServiceProvider;
use WPPB\Providers\CoreServiceProvider;
use WPPB\Providers\FrontendServiceProvider;
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
	 * List of providers.
	 *
	 * @var array
	 */
	protected array $providers = array(
		CoreServiceProvider::class,
		AdminServiceProvider::class,
		FrontendServiceProvider::class,
	);

	/**
	 * Main Plugin instance
	 *
	 * @param string               $plugin_file Main plugin file path.
	 * @param array<string, mixed> $config The plugin configuration.
	 *
	 * @return Plugin
	 */
	public static function instance( string $plugin_file, array $config ): Plugin
	{
		if ( is_null( self::$instance ) ) {
			self::$instance = new self( $plugin_file, $config );
		}

		return self::$instance;
	}

	/**
	 * Cloning is forbidden.
	 */
	public function __clone()
	{
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', '__PLUGIN_TEXTDOMAIN__' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup()
	{
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', '__PLUGIN_TEXTDOMAIN__' ), '1.0.0' );
	}

	/**
	 * Constructor
	 *
	 * @param string               $plugin_file Main plugin file path.
	 * @param array<string, mixed> $config The plugin configuration.
	 */
	private function __construct( string $plugin_file, array $config )
	{
		$this->plugin_file = $plugin_file;
		$this->setup_config( $config );

		$this->container = new Container();

		// Bind the plugin instance itself into the container.
		$this->container->instance( self::class, $this );

		$this->register_services();
		$this->boot_providers();

		$this->core = $this->container->get( Init::class );
		$this->core->init();
	}

	/**
	 * Register the essential services for the plugin.
	 */
	private function register_services(): void
	{
		$this->container->singleton( 'hooker', Hooker::class );
		$this->container->singleton( 'lifecycle', Lifecycle::class );
		$this->container->singleton( 'assets', Assets::class );
		$this->container->singleton( 'i18n', I18n::class );
		$this->container->singleton( Init::class, Init::class );
	}

	/**
	 * Boot the service providers.
	 *
	 * This method registers, resolves, and boots each service provider.
	 */
	private function boot_providers(): void
	{
		foreach ( $this->providers as $provider_class ) {
			$provider = $this->container->get( $provider_class );
			$provider->register();
			$provider->boot();
		}
	}

	/**
	 * Setup the plugin configuration.
	 *
	 * @param array<string, mixed> $config The plugin configuration.
	 */
	private function setup_config( array $config ): void
	{
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
