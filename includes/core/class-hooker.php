<?php
/**
 * Hooker class for managing WordPress hooks.
 *
 * @package WPPB
 */

namespace WPPB\Core;

/** If this file is called directly, abort. */
defined( 'ABSPATH' ) || die;

/**
 * A class that collects and registers WordPress hooks (actions and filters).
 *
 * This class allows for adding hooks throughout the application and then running
 * them all at once at a specific point in the bootstrap process.
 *
 * @since 1.0.0
 */
class Hooker {

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @var array<int, array>
	 */
	protected array $actions = array();

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @var array<int, array>
	 */
	protected array $filters = array();

	/**
	 * Add a WordPress action to the collection.
	 *
	 * Accepts either:
	 *   - (string $hook, callable $callback, null, int $priority = 10, int $accepted_args = 1)
	 *   - (string $hook, object $object, string|null $method = null, int $priority = 10, int $accepted_args = 1)
	 *
	 * If $component is an object and $method is omitted or null, the hook name will be used as the method name.
	 *
	 * @param string          $hook          The name of the action to add.
	 * @param callable|object $component    The callback or object instance.
	 * @param string|null     $method        The method name if using object, or null if using callable or to fallback to hook name.
	 * @param int             $priority      The priority for the action. Default 10.
	 * @param int             $accepted_args The number of accepted arguments. Default 1.
	 * @return self
	 */
	public function add_action( string $hook, $component, ?string $method = null, int $priority = 10, int $accepted_args = 1 ): self {

		$callback = is_object( $component )
			? array( $component, $method ?? $hook )
			: $component;

		$this->actions[] = array(
			'hook'          => $hook,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);

		return $this;
	}

	/**
	 * Add a WordPress filter to the collection.
	 *
	 * Accepts either:
	 *   - (string $hook, callable $callback, null, int $priority = 10, int $accepted_args = 1)
	 *   - (string $hook, object $object, string|null $method = null, int $priority = 10, int $accepted_args = 1)
	 *
	 * If $component is an object and $method is omitted or null, the hook name will be used as the method name.
	 *
	 * @param string          $hook          The name of the filter to add.
	 * @param callable|object $component    The callback or object instance.
	 * @param string|null     $method        The method name if using object, or null if using callable or to fallback to hook name.
	 * @param int             $priority      The priority for the filter. Default 10.
	 * @param int             $accepted_args The number of accepted arguments. Default 1.
	 * @return self
	 */
	public function add_filter( string $hook, $component, ?string $method = null, int $priority = 10, int $accepted_args = 1 ): self {

		$callback = is_object( $component )
			? array( $component, $method ?? $hook )
			: $component;

		$this->filters[] = array(
			'hook'          => $hook,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);

		return $this;
	}

	/**
	 * Register all collected filters and actions with WordPress.
	 *
	 * @return void
	 */
	public function run(): void {

		foreach ( $this->filters as $filter ) {
			add_filter( $filter['hook'], $filter['callback'], $filter['priority'], $filter['accepted_args'] );
		}

		foreach ( $this->actions as $action ) {
			add_action( $action['hook'], $action['callback'], $action['priority'], $action['accepted_args'] );
		}
	}

	/**
	 * Add multiple WordPress actions at once.
	 *
	 * Each item should be an array: [hook, callback|object, method (optional), priority (optional), accepted_args (optional)]
	 * If method is omitted and the second argument is an object, the hook name will be used as the method name (see add_action()).
	 *
	 * @param array<int, array> $actions Array of action configurations. Each item should be an array containing:
	 *                                   - string $hook The action hook name.
	 *                                   - callable|object $component The callback or object instance.
	 *                                   - string|null $method (optional) The method name if using object.
	 *                                   - int $priority (optional) Hook priority, defaults to 10.
	 *                                   - int $accepted_args (optional) Number of arguments, defaults to 1.
	 * @return self
	 */
	public function add_actions( array $actions ): self {

		foreach ( $actions as $args ) {
			$hook          = $args[0] ?? null;
			$component     = $args[1] ?? null;
			$method        = isset( $args[2] ) ? $args[2] : null;
			$priority      = isset( $args[3] ) ? $args[3] : 10;
			$accepted_args = isset( $args[4] ) ? $args[4] : 1;

			$this->add_action( $hook, $component, $method, $priority, $accepted_args );
		}

		return $this;
	}

	/**
	 * Add multiple WordPress filters at once.
	 *
	 * Each item should be an array: [hook, callback|object, method (optional), priority (optional), accepted_args (optional)]
	 * If method is omitted and the second argument is an object, the hook name will be used as the method name (see add_filter()).
	 *
	 * @param array<int, array> $filters Array of filter configurations. Each item should be an array containing:
	 *                                   - string $hook The filter hook name.
	 *                                   - callable|object $component The callback or object instance.
	 *                                   - string|null $method (optional) The method name if using object.
	 *                                   - int $priority (optional) Hook priority, defaults to 10.
	 *                                   - int $accepted_args (optional) Number of arguments, defaults to 1.
	 * @return self
	 */
	public function add_filters( array $filters ): self {

		foreach ( $filters as $args ) {
			$hook          = $args[0] ?? null;
			$component     = $args[1] ?? null;
			$method        = isset( $args[2] ) ? $args[2] : null;
			$priority      = isset( $args[3] ) ? $args[3] : 10;
			$accepted_args = isset( $args[4] ) ? $args[4] : 1;

			$this->add_filter( $hook, $component, $method, $priority, $accepted_args );
		}

		return $this;
	}
}
