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

		// Return existing singleton instance.
		if ( isset( $this->instances[ $id ] ) ) {
			return $this->instances[ $id ];
		}

		// Check if service is registered.
		if ( ! isset( $this->services[ $id ] ) ) {
			throw new \RuntimeException(
				sprintf(
					/* translators: %s: Service identifier */
					esc_html__( 'Service "%s" not found in container.', 'WPPB' ),
					esc_html( $id )
				)
			);
		}

		$service  = $this->services[ $id ];
		$instance = $this->resolve( $service['concrete'] );

		// Store singleton instance.
		if ( $service['singleton'] ) {
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
	 * Resolve a concrete implementation
	 *
	 * @since 1.0.0
	 * @param callable|string $concrete Service implementation.
	 * @return mixed
	 * @throws \RuntimeException If concrete cannot be resolved.
	 */
	private function resolve( $concrete ) {
		// If it's a callable, execute it.
		if ( is_callable( $concrete ) ) {
			return $concrete( $this );
		}

		// If it's a class name, instantiate it.
		if ( is_string( $concrete ) && class_exists( $concrete ) ) {
			return new $concrete();
		}

		// If it's already an object, return it.
		if ( is_object( $concrete ) ) {
			return $concrete;
		}

		throw new \RuntimeException( esc_html__( 'Cannot resolve concrete.', 'WPPB' ) );
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
		$this->services[ $id ] = $value;
	}

	/**
	 * Add a service to the container.
	 *
	 * @param string $id The service ID.
	 * @param mixed  $service The service instance or callable.
	 * @param bool   $shared Whether the service should be shared.
	 */
	public function add( string $id, $service, bool $shared = false ): void {
		$this->services[ $id ] = $service;

		if ( $shared ) {
			$this->shared[ $id ] = true;
		}
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
					esc_html__( 'Service "%s" not found in container.', 'WPPB' ),
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
