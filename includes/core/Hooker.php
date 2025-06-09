<?php
/**
 * Hooker class for managing WordPress hooks.
 *
 * @package WPPB
 */

namespace WPPB\Core;

/**
 * If this file is called directly, abort.
 */
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
	 * @param string   $hook          The name of the action to add.
	 * @param callable $callback      The callback to be executed.
	 * @param int      $priority      Optional. Used to specify the order in which the functions
	 *                                associated with a particular action are executed. Default 10.
	 * @param int      $accepted_args Optional. The number of arguments the function accepts. Default 1.
	 * @return self
	 */
	public function add_action( string $hook, callable $callback, int $priority = 10, int $accepted_args = 1 ): self {
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
	 * @param string   $hook          The name of the filter to add.
	 * @param callable $callback      The callback to be executed.
	 * @param int      $priority      Optional. Used to specify the order in which the functions
	 *                                associated with a particular action are executed. Default 10.
	 * @param int      $accepted_args Optional. The number of arguments the function accepts. Default 1.
	 * @return self
	 */
	public function add_filter( string $hook, callable $callback, int $priority = 10, int $accepted_args = 1 ): self {
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
}
