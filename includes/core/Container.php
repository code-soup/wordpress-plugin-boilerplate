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
use ReflectionClass;
use ReflectionParameter;

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
	 * The container's bindings.
	 *
	 * @var array<string, array{concrete: callable|string, shared: bool}>
	 */
	private array $bindings = array();

	/**
	 * The container's shared instances.
	 *
	 * @var array<string, mixed>
	 */
	private array $instances = array();

	/**
	 * The container's aliases.
	 *
	 * @var array<string, string>
	 */
	private array $aliases = array();

	/**
	 * Bind a new service into the container.
	 *
	 * @param string          $id       The abstract identifier.
	 * @param callable|string $concrete The concrete implementation.
	 * @param bool            $shared   Whether the service should be a singleton.
	 *
	 * @return void
	 */
	public function bind( string $id, $concrete, bool $shared = false ): void {
		$this->bindings[ $id ] = compact( 'concrete', 'shared' );
	}

	/**
	 * Bind a new singleton service into the container.
	 *
	 * @param string          $id       The abstract identifier.
	 * @param callable|string $concrete The concrete implementation.
	 *
	 * @return void
	 */
	public function singleton( string $id, $concrete ): void {
		$this->bind( $id, $concrete, true );
	}

	/**
	 * Bind an existing instance as a singleton.
	 *
	 * @param string $id       The abstract identifier.
	 * @param mixed  $instance The existing instance.
	 *
	 * @return void
	 */
	public function instance( string $id, $instance ): void {
		$this->instances[ $id ] = $instance;
	}

	/**
	 * Alias a service to a different name.
	 *
	 * @param string $id    The abstract identifier.
	 * @param string $alias The alias.
	 *
	 * @return void
	 */
	public function alias( string $id, string $alias ): void {
		$this->aliases[ $alias ] = $id;
	}

	/**
	 * Finds an entry of the container by its identifier and returns it.
	 *
	 * @param string $id The identifier of the entry to look for.
	 *
	 * @return mixed The entry.
	 * @throws \Exception If the entry is not found.
	 */
	public function get( string $id ) {
		$id = $this->aliases[ $id ] ?? $id;

		if ( isset( $this->instances[ $id ] ) ) {
			return $this->instances[ $id ];
		}

		if ( ! isset( $this->bindings[ $id ] ) ) {
			if ( class_exists( $id ) ) {
				return $this->resolve( $id );
			}

			throw new \Exception( sprintf( 'Service "%s" not found in container.', esc_html( $id ) ) );
		}

		$binding = $this->bindings[ $id ];

		$instance = $this->resolve( $binding['concrete'] );

		if ( $binding['shared'] ) {
			$this->instances[ $id ] = $instance;
		}

		return $instance;
	}

	/**
	 * Returns true if the container can return an entry for the given identifier.
	 *
	 * @param string $id The identifier of the entry to look for.
	 *
	 * @return bool
	 */
	public function has( string $id ): bool {
		$id = $this->aliases[ $id ] ?? $id;

		return isset( $this->bindings[ $id ] ) || isset( $this->instances[ $id ] );
	}

	/**
	 * Resolve a service from the container.
	 *
	 * @param callable|string $concrete The concrete implementation.
	 *
	 * @return mixed
	 * @throws \Exception If the service cannot be resolved.
	 */
	private function resolve( $concrete ) {
		if ( is_callable( $concrete ) ) {
			return $concrete( $this );
		}

		if ( ! class_exists( $concrete ) ) {
			throw new \Exception( sprintf( 'Cannot resolve concrete class: %s.', esc_html( $concrete ) ) );
		}

		$reflector = new ReflectionClass( $concrete );

		if ( ! $reflector->isInstantiable() ) {
			throw new \Exception( sprintf( 'Class "%s" is not instantiable.', esc_html( $concrete ) ) );
		}

		$constructor = $reflector->getConstructor();

		if ( is_null( $constructor ) ) {
			return new $concrete();
		}

		$dependencies = array_map(
			function ( ReflectionParameter $param ) use ( $concrete ) {
				$type = $param->getType();

				if ( ! $type ) {
					throw new \Exception( sprintf( 'Cannot resolve class dependency "%s" in class "%s".', esc_html( $param->getName() ), esc_html( $concrete ) ) );
				}

				if ( $type->isBuiltin() ) {
					throw new \Exception( sprintf( 'Cannot resolve built-in type "%s" in class "%s".', esc_html( $type->getName() ), esc_html( $concrete ) ) );
				}

				return $this->get( $type->getName() );
			},
			$constructor->getParameters()
		);

		return $reflector->newInstanceArgs( $dependencies );
	}

	/**
	 * Clear all services and instances
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function clear(): void {
		$this->bindings  = array();
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
		return array_keys( $this->bindings );
	}

	/**
	 * Set a service in the container
	 *
	 * @param string $id    The service ID.
	 * @param mixed  $value The service instance or value.
	 */
	public function set( string $id, $value ): void {
		$this->bind( $id, $value, true );
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

		if ( is_callable( $this->bindings[ $id ]['concrete'] ) ) {
			return $this->bindings[ $id ]['concrete']( $this );
		}

		return $this->bindings[ $id ]['concrete'];
	}

	/**
	 * Remove a service from the container.
	 *
	 * @param string $id The service ID.
	 */
	public function remove( string $id ): void {
		unset( $this->bindings[ $id ], $this->instances[ $id ] );
	}

	/**
	 * Get all services from the container.
	 *
	 * @return array
	 */
	public function get_services(): array {
		return $this->bindings;
	}

	/**
	 * Get all shared services from the container.
	 *
	 * @return array
	 */
	public function get_shared_services(): array {
		return array_filter(
			$this->bindings,
			function ( $binding ) {
				return $binding['shared'];
			}
		);
	}

	/**
	 * Set all services in the container.
	 *
	 * @param array $services The services to set.
	 */
	public function set_services( array $services ): void {
		$this->bindings = $services;
	}

	/**
	 * Set all shared services in the container.
	 *
	 * @param array $shared The shared services to set.
	 */
	public function set_shared_services( array $shared ): void {
		$this->bindings = array_merge(
			$this->bindings,
			array_filter(
				$shared,
				function ( $binding ) {
					return $binding['shared'];
				}
			)
		);
	}
}
