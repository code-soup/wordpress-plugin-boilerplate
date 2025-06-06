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
	 * Register a service.
	 *
	 * @param string $id The service ID.
	 * @param mixed  $service The service instance or callable.
	 * @param bool   $shared Whether the service should be shared.
	 */
	public function register_service( string $id, $service, bool $shared = false ): void {
		$this->container->add( $id, $service, $shared );
	}

	/**
	 * Get a service.
	 *
	 * @param string $id The service ID.
	 *
	 * @return mixed
	 */
	public function get_service( string $id ) {
		return $this->container->get( $id );
	}

	/**
	 * Get a new instance of a service.
	 *
	 * @param string $id The service ID.
	 *
	 * @return mixed
	 */
	public function get_new_service( string $id ) {
		return $this->container->get_new( $id );
	}

	/**
	 * Check if a service exists.
	 *
	 * @param string $id The service ID.
	 *
	 * @return bool
	 */
	public function has_service( string $id ): bool {
		return $this->container->has( $id );
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
	 * @param \WPPB\Core\Container $container The DI container.
	 * @param string               $id Service identifier.
	 * @param callable|string      $concrete Service implementation.
	 * @return void
	 */
	protected function singleton( \WPPB\Core\Container $container, string $id, $concrete ): void {
		$container->singleton( $id, $concrete );
		$this->provides[] = $id;
	}

	/**
	 * Register a factory service
	 *
	 * @since 1.0.0
	 * @param \WPPB\Core\Container $container The DI container.
	 * @param string               $id Service identifier.
	 * @param callable|string      $concrete Service implementation.
	 * @return void
	 */
	protected function factory( \WPPB\Core\Container $container, string $id, $concrete ): void {
		$container->factory( $id, $concrete );
		$this->provides[] = $id;
	}

	/**
	 * Register an alias
	 *
	 * @since 1.0.0
	 * @param \WPPB\Core\Container $container The DI container.
	 * @param string               $alias Alias name.
	 * @param string               $id Original service identifier.
	 * @return void
	 */
	protected function alias( \WPPB\Core\Container $container, string $alias, string $id ): void {
		$container->alias( $alias, $id );
	}
}
