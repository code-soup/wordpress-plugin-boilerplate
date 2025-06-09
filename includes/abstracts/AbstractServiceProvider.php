<?php
/**
 * Abstract Service Provider.
 *
 * @package WPPB
 */

namespace WPPB\Abstracts;

use WPPB\Core\Container;
use WPPB\Interfaces\ServiceProviderInterface;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * AbstractServiceProvider class.
 */
abstract class AbstractServiceProvider implements ServiceProviderInterface {

	/**
	 * The container instance.
	 *
	 * @var Container
	 */
	protected Container $container;

	/**
	 * The list of provided services.
	 *
	 * @var array
	 */
	protected array $provides = array();

	/**
	 * Whether the provider has been booted
	 *
	 * @var bool
	 * @since 1.0.0
	 */
	protected bool $booted = false;

	/**
	 * AbstractServiceProvider constructor.
	 *
	 * @param Container $container The container instance.
	 */
	public function __construct( Container $container ) {
		$this->container = $container;
	}

	/**
	 * Get the container instance.
	 *
	 * @return Container
	 */
	public function get_container(): Container {
		return $this->container;
	}

	/**
	 * Get the list of provided services.
	 *
	 * @return array
	 */
	public function get_provides(): array {
		return $this->provides;
	}

	/**
	 * Set the list of provided services.
	 *
	 * @param array $provides The list of provided services.
	 */
	public function set_provides( array $provides ): void {
		$this->provides = $provides;
	}

	/**
	 * Check if the service provider provides a certain service.
	 *
	 * @param string $service The service to check.
	 *
	 * @return bool
	 */
	public function provides( string $service ): bool {
		return in_array( $service, $this->provides, true );
	}

	/**
	 * Register the service provider.
	 */
	public function register(): void {
		// Can be implemented by child classes.
	}

	/**
	 * Boot the service provider.
	 */
	public function boot(): void {
		// Can be implemented by child classes.
	}

	/**
	 * Check if the provider has been booted
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function isBooted(): bool {
		return $this->booted;
	}

	/**
	 * Register a singleton service
	 *
	 * @since 1.0.0
	 *
	 * @param string          $id       Service identifier.
	 * @param callable|string $concrete Service implementation.
	 *
	 * @return void
	 */
	protected function singleton( string $id, $concrete ): void {
		$this->container->singleton( $id, $concrete );
	}

	/**
	 * Register a factory service
	 *
	 * @since 1.0.0
	 *
	 * @param string          $id       Service identifier.
	 * @param callable|string $concrete Service implementation.
	 *
	 * @return void
	 */
	protected function factory( string $id, $concrete ): void {
		$this->container->bind( $id, $concrete );
	}

	/**
	 * Register an alias
	 *
	 * @since 1.0.0
	 *
	 * @param string $id    Original service identifier.
	 * @param string $alias Alias name.
	 *
	 * @return void
	 */
	protected function alias( string $id, string $alias ): void {
		$this->container->alias( $id, $alias );
	}
}
