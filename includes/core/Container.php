<?php

declare(strict_types=1);

namespace WPPB\Core;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Dependency Injection Container
 *
 * A simple but effective DI container for managing plugin dependencies.
 * Supports singleton services, factory services, and automatic resolution.
 *
 * @since 1.0.0
 */
class Container {

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
	 * Register a service in the container
	 *
	 * @since 1.0.0
	 * @param string $id Service identifier
	 * @param callable|string $concrete Service implementation
	 * @param bool $singleton Whether to treat as singleton
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
	 * @param string $id Service identifier
	 * @param callable|string $concrete Service implementation
	 * @return self
	 */
	public function singleton( string $id, $concrete ): self {
		return $this->register( $id, $concrete, true );
	}

	/**
	 * Register a factory service (new instance each time)
	 *
	 * @since 1.0.0
	 * @param string $id Service identifier
	 * @param callable|string $concrete Service implementation
	 * @return self
	 */
	public function factory( string $id, $concrete ): self {
		return $this->register( $id, $concrete, false );
	}

	/**
	 * Register an alias for a service
	 *
	 * @since 1.0.0
	 * @param string $alias Alias name
	 * @param string $id Original service identifier
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
	 * @param string $id Service identifier
	 * @return mixed
	 * @throws \RuntimeException If service cannot be resolved
	 */
	public function get( string $id ) {
		// Resolve alias if exists
		$id = $this->aliases[ $id ] ?? $id;

		// Return existing singleton instance
		if ( isset( $this->instances[ $id ] ) ) {
			return $this->instances[ $id ];
		}

		// Check if service is registered
		if ( ! isset( $this->services[ $id ] ) ) {
			throw new \RuntimeException( "Service '{$id}' not found in container." );
		}

		$service  = $this->services[ $id ];
		$instance = $this->resolve( $service['concrete'] );

		// Store singleton instance
		if ( $service['singleton'] ) {
			$this->instances[ $id ] = $instance;
		}

		return $instance;
	}

	/**
	 * Check if a service is registered
	 *
	 * @since 1.0.0
	 * @param string $id Service identifier
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
	 * @param string $id Service identifier
	 * @param object $instance Service instance
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
	 * @param callable|string $concrete Service implementation
	 * @return mixed
	 * @throws \RuntimeException If concrete cannot be resolved
	 */
	private function resolve( $concrete ) {
		// If it's a callable, execute it
		if ( is_callable( $concrete ) ) {
			return $concrete( $this );
		}

		// If it's a class name, instantiate it
		if ( is_string( $concrete ) && class_exists( $concrete ) ) {
			return new $concrete();
		}

		// If it's already an object, return it
		if ( is_object( $concrete ) ) {
			return $concrete;
		}

		throw new \RuntimeException( 'Cannot resolve concrete: ' . print_r( $concrete, true ) );
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
}
