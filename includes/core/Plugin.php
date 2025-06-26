<?php
/**
 * Main Plugin class.
 *
 * @package WPPB
 */

declare( strict_types=1 );

namespace WPPB\Core;

use WPPB\Providers\AdminServiceProvider;
use WPPB\Providers\FrontendServiceProvider;
use WPPB\Traits\HelpersTrait;
use WPPB\Traits\RequirementChecksTrait;

/**
 * The main plugin class
 */
final class Plugin {

	use HelpersTrait;
	use RequirementChecksTrait;

	/**
	 * The single instance of the class
	 *
	 * @var Plugin|null
	 */
	private static ?Plugin $instance = null;

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

		// Bind the plugin instance itself into the container.
		$this->container->instance( self::class, $this );

		// Bind the container instance to prevent auto-resolution.
		$this->container->instance( Container::class, $this->container );

		$this->bind_services();
	}

	/**
	 * Boots providers and fires all registered hooks.
	 * This is the main entry point for the plugin logic.
	 */
	public function run(): void {
		if ( ! $this->is_compatible() ) {
			return;
		}

		$this->load_providers();
		$this->get( 'hooker' )->run();
	}

	/**
	 * Get a service from the container by $alias.
	 *
	 * @param string $alias The service name to retrieve.
	 *
	 * @return mixed
	 */
	public function get( string $alias ) {
		return $this->container->get( $alias );
	}

	/**
	 * Register the essential services for the plugin.
	 */
	private function bind_services(): void {
		$this->container->singleton( 'hooker', Hooker::class );
		$this->container->singleton( 'assets', Assets::class );
		$this->container->singleton( 'i18n', I18n::class );
	}

	/**
	 * Load and boot all service providers.
	 */
	private function load_providers(): void {
		foreach ( $this->providers as $provider_class ) {
			$provider = new $provider_class( $this->container );
			$provider->register();
			$provider->boot();
		}
	}

	/**
	 * Check if the plugin is compatible with the current environment.
	 *
	 * @return bool
	 * @throws \Exception If a compatibility check fails.
	 */
	private function is_compatible(): bool {
		try {
			self::run_requirement_checks( $this->config );

			return true;
		} catch ( \Exception $e ) {
			add_action(
				'admin_notices',
				function () use ( $e ) {
					echo '<div class="notice notice-error"><p>' . esc_html( $e->getMessage() ) . '</p></div>';
				}
			);

			return false;
		}
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
