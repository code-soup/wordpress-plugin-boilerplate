<?php
/**
 * Core Init Class.
 *
 * @package WPPB
 */

namespace WPPB\Core;

use WPPB\Core\Container;
use WPPB\Core\Hooker;
use WPPB\Core\Lifecycle;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
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
	 * Plugin instance.
	 *
	 * @var Init
	 */
	private static $instance = null;

	/**
	 * The dependency injection container
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var Container|null The DI container instance
	 */
	protected ?Container $container = null;

	/**
	 * List of providers.
	 *
	 * @var array
	 */
	protected $providers = array(
		\WPPB\Providers\CoreServiceProvider::class,
		\WPPB\Providers\AdminServiceProvider::class,
		\WPPB\Providers\FrontendServiceProvider::class,
	);

	/**
	 * Plugin lifecycle manager
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var Lifecycle|null
	 */
	protected ?Lifecycle $lifecycle = null;

	/**
	 * The Hooker instance.
	 *
	 * @var Hooker
	 */
	protected Hooker $hooker;

	/**
	 * Class constructor.
	 */
	private function __construct() {
		$this->container = new Container();
		$this->hooker    = new Hooker();
	}

	/**
	 * Get the plugin instance.
	 *
	 * @return Init
	 */
	public static function get_instance(): Init {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Singletons should not be cloneable.
	 *
	 * @since 1.0.0
	 * @throws \Exception If clone is attempted.
	 * @return void
	 */
	private function __clone() {
		throw new \Exception( 'Cannot clone ' . __CLASS__ );
	}

	/**
	 * Singletons should not be restorable from strings.
	 *
	 * @since 1.0.0
	 * @throws \Exception If unserialize is attempted.
	 * @return void
	 */
	public function __wakeup() {
		throw new \Exception( 'Cannot unserialize ' . __CLASS__ );
	}

	/**
	 * Initialize the plugin.
	 *
	 * @throws \Exception If the plugin is not compatible.
	 */
	public function init(): void {
		if ( ! $this->is_compatible() ) {
			return;
		}

		$this->register_providers();
		$this->boot_providers();
	}

	/**
	 * Register the service providers.
	 */
	private function register_providers(): void {
		foreach ( $this->providers as $provider_class ) {
			$provider = new $provider_class( $this->container );
			$provider->register();
		}
	}

	/**
	 * Boot the service providers.
	 */
	private function boot_providers(): void {
		foreach ( $this->providers as $provider_class ) {
			$provider = $this->container->get( $provider_class );
			if ( $provider ) {
				$provider->boot();
			}
		}
	}

	/**
	 * Check if the plugin is compatible with the current environment.
	 *
	 * @return bool
	 * @throws \Exception If a compatibility check fails.
	 */
	private function is_compatible(): bool {
		$lifecycle = $this->container->get( 'lifecycle' );

		try {
			$lifecycle->check_requirements();
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
	 * Magic method to get a service from the container.
	 *
	 * @param string $name The name of the service to get.
	 *
	 * @return mixed
	 */
	public function __get( string $name ) {
		return $this->container->get( $name );
	}

	/**
	 * Magic method to check if a service is set in the container.
	 *
	 * @param string $name The name of the service to check.
	 *
	 * @return bool
	 */
	public function __isset( string $name ): bool {
		return $this->container->has( $name );
	}

	/**
	 * Get a service from the container.
	 *
	 * @param string $name The name of the service to get.
	 *
	 * @return mixed
	 */
	public function get( string $name ) {
		return $this->container->get( $name );
	}

	/**
	 * Set a service in the container.
	 *
	 * @param string $name  The name of the service to set.
	 * @param mixed  $value The service to set.
	 */
	public function set( string $name, $value ): void {
		$this->container->set( $name, $value );
	}

	/**
	 * Get the plugin container.
	 *
	 * @return Container
	 */
	public function get_container(): Container {
		return $this->container;
	}

	/**
	 * Get the lifecycle manager
	 *
	 * @since 1.0.0
	 * @return Lifecycle The lifecycle manager
	 * @throws \RuntimeException If lifecycle is not initialized.
	 */
	public function get_lifecycle(): Lifecycle {
		if ( null === $this->lifecycle ) {
			throw new \RuntimeException( 'Lifecycle not initialized. Call init() first.' );
		}

		return $this->lifecycle;
	}

	/**
	 * Legacy method: Get the hooker instance
	 *
	 * @since 1.0.0
	 * @return \WPPB\Core\Hooker The hooker instance
	 * @deprecated Use get('hooker') instead
	 */
	public function get_hooker(): \WPPB\Core\Hooker {
		return $this->get( 'hooker' );
	}

	/**
	 * Legacy method: Get the assets instance
	 *
	 * @since 1.0.0
	 * @return \WPPB\Core\Assets The assets instance
	 * @deprecated Use get('assets') instead
	 */
	public function get_assets(): \WPPB\Core\Assets {
		return $this->get( 'assets' );
	}
}
