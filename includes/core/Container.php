<?php
/**
 * Container class.
 *
 * @package WPPB
 */

declare(strict_types=1);

namespace WPPB\Core;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

use Psr\Container\ContainerInterface;

/**
 * Dependency Injection Container
 *
 * A simple but effective DI container for managing plugin dependencies.
 * Supports singleton services, factory services, and automatic resolution.
 *
 * @since 1.0.0
 */
class Container implements ContainerInterface {

	/**
	 * Registered services
	 *
	 * @var array<string, array>
	 * @since 1.0.0
	 */
	private array $services = array();

	/**
	 * Singleton instances
	 *
	 * @var array<string, object>
	 * @since 1.0.0
	 */
	private array $instances = array();

	/**
	 * Service aliases
	 *
	 * @var array<string, string>
	 * @since 1.0.0
	 */
	private array $aliases = array();

	/**
	 * The container's shared services.
	 *
	 * @var array
	 */
	private array $shared = array();

	/**
	 * Register a service in the container
	 *
	 * @since 1.0.0
	 * @param string          $id Service identifier.
	 * @param callable|string $concrete Service implementation.
	 * @param bool            $singleton Whether to treat as singleton.
	 * @return self
	 */
	public function register( string $id, $concrete, bool $singleton = true ): self {
		$this->services[ $id ] = array(
			'concrete'  => $concrete,
			'singleton' => $singleton,
		);

		return $this;
	}

	/**
	 * Register a singleton service
	 *
	 * @since 1.0.0
	 * @param string          $id Service identifier.
	 * @param callable|string $concrete Service implementation.
	 * @return self
	 */
	public function singleton( string $id, $concrete ): self {
		return $this->register( $id, $concrete, true );
	}

	/**
	 * Register a factory service (new instance each time)
	 *
	 * @since 1.0.0
	 * @param string          $id Service identifier.
	 * @param callable|string $concrete Service implementation.
	 * @return self
	 */
	public function factory( string $id, $concrete ): self {
		return $this->register( $id, $concrete, false );
	}

	/**
	 * Register an alias for a service
	 *
	 * @since 1.0.0
	 * @param string $alias Alias name.
	 * @param string $id Original service identifier.
	 * @return self
	 */
	public function alias( string $alias, string $id ): self {
		$this->aliases[ $alias ] = $id;
		return $this;
	}

	/**
	 * Resolve a service from the container
	 *
	 * @since 1.0.0
	 * @param string $id Service identifier.
	 * @return mixed
	 * @throws \RuntimeException If service cannot be resolved.
	 */
	public function get( string $id ) {
		// Resolve alias if exists.
		$id = $this->aliases[ $id ] ?? $id;

		// If it's a singleton and we already have an instance, return it.
		if ( isset( $this->instances[ $id ] ) ) {
			return $this->instances[ $id ];
		}

		// Check if service is registered.
		if ( ! isset( $this->services[ $id ] ) ) {
			throw new \RuntimeException(
				esc_html(
					sprintf(
						/* translators: %s: Service identifier */
						__( 'Service "%s" not found in container.', '__PLUGIN_TEXTDOMAIN__' ),
						esc_html( $id )
					)
				)
			);
		}

		// Resolve the service (this might create a new object).
		$instance = $this->resolve( $id );

		// If it's a singleton, store the new instance that we just created.
		if ( isset( $this->services[ $id ] ) && $this->services[ $id ]['singleton'] ) {
			$this->instances[ $id ] = $instance;
		}

		return $instance;
	}

	/**
	 * Check if a service is registered
	 *
	 * @since 1.0.0
	 * @param string $id Service identifier.
	 * @return bool
	 */
	public function has( string $id ): bool {
		$id = $this->aliases[ $id ] ?? $id;
		return isset( $this->services[ $id ] );
	}

	/**
	 * Bind an existing instance to the container
	 *
	 * @since 1.0.0
	 * @param string $id Service identifier.
	 * @param object $instance Service instance.
	 * @return self
	 */
	public function instance( string $id, object $instance ): self {
		$this->instances[ $id ] = $instance;
		return $this;
	}

	/**
	 * Resolve the service from the container
	 *
	 * @param string $id Service identifier.
	 * @return mixed
	 * @throws \RuntimeException If the service is not found or cannot be resolved.
	 */
	private function resolve( string $id ) {
		if ( ! isset( $this->services[ $id ] ) ) {
			throw new \RuntimeException(
				esc_html(
					sprintf(
						/* translators: %s: Service ID. */
						__( 'Service "%s" not found in container.', '__PLUGIN_TEXTDOMAIN__' ),
						$id
					)
				)
			);
		}

		$service = $this->services[ $id ];

		// If the concrete implementation is a callable (a factory), just call it.
		if ( is_callable( $service['concrete'] ) ) {
			return $service['concrete']( $this );
		}

		// Otherwise, assume it's a class name and try to instantiate it.
		// This will only work for classes with no constructor dependencies.
		$concrete = $service['concrete'];
		if ( ! class_exists( $concrete ) ) {
			throw new \RuntimeException(
				esc_html(
					sprintf(
						/* translators: %s: Concrete class name. */
						__( 'Cannot resolve concrete class: %s.', '__PLUGIN_TEXTDOMAIN__' ),
						$concrete
					)
				)
			);
		}

		return new $concrete();
	}

	/**
	 * Clear all services and instances
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function clear(): void {
		$this->services  = array();
		$this->instances = array();
		$this->aliases   = array();
	}

	/**
	 * Get all registered service IDs
	 *
	 * @since 1.0.0
	 * @return array<string>
	 */
	public function get_service_ids(): array {
		return array_keys( $this->services );
	}

	/**
	 * Set a service in the container
	 *
	 * @param string $id    The service ID.
	 * @param mixed  $value The service instance or value.
	 */
	public function set( string $id, $value ): void {
		$this->register( $id, $value, true );
	}

	/**
	 * Get a new instance of a service from the container.
	 *
	 * @param string $id The service ID.
	 *
	 * @return mixed
	 * @throws \Exception If the service is not found.
	 */
	public function get_new( string $id ) {
		if ( ! $this->has( $id ) ) {
			// translators: %s is the service ID.
			throw new \Exception(
				sprintf(
					/* translators: %s: Service identifier */
					esc_html__( 'Service "%s" not found in container.', '__PLUGIN_TEXTDOMAIN__' ),
					esc_html( $id )
				)
			);
		}

		if ( is_callable( $this->services[ $id ] ) ) {
			return $this->services[ $id ]( $this );
		}

		return $this->services[ $id ];
	}

	/**
	 * Remove a service from the container.
	 *
	 * @param string $id The service ID.
	 */
	public function remove( string $id ): void {
		unset( $this->services[ $id ], $this->shared[ $id ] );
	}

	/**
	 * Get all services from the container.
	 *
	 * @return array
	 */
	public function get_services(): array {
		return $this->services;
	}

	/**
	 * Get all shared services from the container.
	 *
	 * @return array
	 */
	public function get_shared_services(): array {
		return $this->shared;
	}

	/**
	 * Set all services in the container.
	 *
	 * @param array $services The services to set.
	 */
	public function set_services( array $services ): void {
		$this->services = $services;
	}

	/**
	 * Set all shared services in the container.
	 *
	 * @param array $shared The shared services to set.
	 */
	public function set_shared_services( array $shared ): void {
		$this->shared = $shared;
	}
}
