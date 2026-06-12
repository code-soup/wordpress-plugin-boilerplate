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
	 * Register the service provider.
	 */
	public function register(): void {
		// Can be implemented by child classes.
	}

	/**
	 * Boot the service provider.
	 */
	public function boot(): void {
		if ( $this->booted ) {
			return;
		}

		$this->booted = true;
		// Can be implemented by child classes.
	}

	/**
	 * Check if the provider has been booted
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_booted(): bool {
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
