<?php

declare(strict_types=1);

namespace WPPB\Core;

use WPPB\Core\Container;
use WPPB\Core\Lifecycle;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

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
	 *
	 * @var self|null
	 * @since 1.0.0
	 */
	private static $instance;


	/**
	 * Define plugin constants
	 *
	 * @var array<string, string>
	 * @since 1.0.0
	 */
	public static array $constants = array();


	/**
	 * The dependency injection container
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var Container|null The DI container instance
	 */
	protected ?Container $container = null;

	/**
	 * Service providers
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var array<\WPPB\Interfaces\ServiceProviderInterface>
	 */
	protected array $providers = array();

	/**
	 * Plugin lifecycle manager
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var Lifecycle|null
	 */
	protected ?Lifecycle $lifecycle = null;


	/**
	 * Make constructor protected, to prevent direct instantiation
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {}


	/**
	 * Main Instance.
	 *
	 * Ensures only one instance is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @return self Main instance.
	 */
	public static function get_instance(): self {
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
	 * Run everything on init
	 *
	 * @since 1.0.0
	 * @return void
	 * @throws \RuntimeException If initialization fails
	 */
	public function init(): void {
		try {
			// Initialize the dependency injection container
			$this->container = new Container();

			// Initialize lifecycle manager
			$this->lifecycle = new Lifecycle();

			// Register service providers
			$this->register_providers();

			// Boot all service providers
			$this->boot_providers();

		} catch ( \Exception $e ) {
			// Log the error and re-throw
			error_log( 'Plugin initialization failed: ' . $e->getMessage() );
			throw new \RuntimeException( 'Plugin initialization failed: ' . $e->getMessage(), 0, $e );
		}
	}


	/**
	 * Register all service providers
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function register_providers(): void {
		// Core services (always loaded)
		$this->providers[] = new \WPPB\Providers\CoreServiceProvider();

		// Context-specific providers
		if ( is_admin() ) {
			$this->providers[] = new \WPPB\Providers\AdminServiceProvider();
		} else {
			$this->providers[] = new \WPPB\Providers\FrontendServiceProvider();
		}

		// Register all providers with the container
		foreach ( $this->providers as $provider ) {
			$provider->register( $this->container );
		}
	}

	/**
	 * Boot all service providers
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function boot_providers(): void {
		foreach ( $this->providers as $provider ) {
			$provider->boot( $this->container );
		}
	}

	/**
	 * Get the dependency injection container
	 *
	 * @since 1.0.0
	 * @return Container The DI container
	 * @throws \RuntimeException If container is not initialized.
	 */
	public function get_container(): Container {
		if ( null === $this->container ) {
			throw new \RuntimeException( 'Container not initialized. Call init() first.' );
		}

		return $this->container;
	}

	/**
	 * Get a service from the container
	 *
	 * @since 1.0.0
	 * @param string $id Service identifier
	 * @return mixed The resolved service
	 * @throws \RuntimeException If container is not initialized.
	 */
	public function get( string $id ) {
		return $this->get_container()->get( $id );
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


	public function set_constants( array $constants ): void {
		self::$constants = $constants;
	}
}
