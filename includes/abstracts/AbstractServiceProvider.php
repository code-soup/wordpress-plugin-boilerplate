<?php

declare(strict_types=1);

namespace WPPB\Abstracts;

use WPPB\Interfaces\ServiceProviderInterface;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Abstract Service Provider
 *
 * Base class for service providers that provides common functionality
 * and implements the ServiceProviderInterface.
 *
 * @since 1.0.0
 */
abstract class AbstractServiceProvider implements ServiceProviderInterface {

	/**
	 * Services provided by this provider
	 *
	 * @var array<string>
	 * @since 1.0.0
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
	 * Boot services after all providers have been registered
	 *
	 * Default implementation does nothing. Override in child classes as needed.
	 *
	 * @since 1.0.0
	 * @param \WPPB\Core\Container $container The DI container
	 * @return void
	 */
	public function boot( \WPPB\Core\Container $container ): void {
		$this->booted = true;
	}

	/**
	 * Get the services provided by this provider
	 *
	 * @since 1.0.0
	 * @return array<string> Array of service identifiers
	 */
	public function provides(): array {
		return $this->provides;
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
	 * @param \WPPB\Core\Container $container The DI container
	 * @param string $id Service identifier
	 * @param callable|string $concrete Service implementation
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
	 * @param \WPPB\Core\Container $container The DI container
	 * @param string $id Service identifier
	 * @param callable|string $concrete Service implementation
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
	 * @param \WPPB\Core\Container $container The DI container
	 * @param string $alias Alias name
	 * @param string $id Original service identifier
	 * @return void
	 */
	protected function alias( \WPPB\Core\Container $container, string $alias, string $id ): void {
		$container->alias( $alias, $id );
	}
}
