<?php

namespace WPPB;

use WPPB\Interfaces\HookerInterface;

// Exit if accessed directly
defined( 'WPINC' ) || die;

/**
 * @file
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 * 
 * @since 1.0.0
 */
class Hooker implements HookerInterface {

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var array<int, array> The actions registered with WordPress to fire when the plugin loads.
	 */
	protected $actions;

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var array<int, array> The filters registered with WordPress to fire when the plugin loads.
	 */
	protected $filters;

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->actions = array();
		$this->filters = array();
	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since 1.0.0
	 * @param string $hook             The name of the WordPress action that is being registered.
	 * @param object $component        A reference to the instance of the object on which the action is defined.
	 * @param string $callback         The name of the function definition on the $component.
	 * @param int    $priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param int    $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 * @return void
	 */
	public function add_action(string $hook, object $component, string $callback = '', int $priority = 10, int $accepted_args = 1): void {
		// Custom callback method or fallback to same as hook
		$method = (!empty($callback))
			? $callback
			: $hook;

		$this->actions = $this->add(
			$this->actions,
			$hook,
			$component,
			$method,
			$priority,
			$accepted_args
		);
	}

	/**
	 * Add array of actions at once
	 *
	 * @since 1.0.0
	 * @param array<int, array> $actions Array of actions to add.
	 * @return void
	 */
	public function add_actions(array $actions = []): void {
		foreach ($actions as $action) {
			$hook          = isset($action[0]) ? $action[0] : '';
			$component     = isset($action[1]) ? $action[1] : '';
			$callback      = isset($action[2]) ? $action[2] : '';
			$priority      = isset($action[3]) ? $action[3] : 10;
			$accepted_args = isset($action[4]) ? $action[4] : 1;

			$this->add_action($hook, $component, $callback, $priority, $accepted_args);
		}
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since 1.0.0
	 * @param string $hook             The name of the WordPress filter that is being registered.
	 * @param object $component        A reference to the instance of the object on which the filter is defined.
	 * @param string $callback         The name of the function definition on the $component.
	 * @param int    $priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param int    $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 * @return void
	 */
	public function add_filter(string $hook, object $component, string $callback = '', int $priority = 10, int $accepted_args = 1): void {
		// Custom callback method or fallback to same as hook
		$method = ( ! empty( $callback ) )
			? $callback
			: $hook;

		$this->filters = $this->add(
			$this->filters,
			$hook,
			$component,
			$method,
			$priority,
			$accepted_args
		);
	}

	/**
	 * Add array of filters at once
	 *
	 * @since 1.0.0
	 * @param array<int, array> $filters Array of filters to add.
	 * @return void
	 */
	public function add_filters(array $filters = []): void {
		foreach ($filters as $filter) {
			$hook          = isset($filter[0]) ? $filter[0] : '';
			$component     = isset($filter[1]) ? $filter[1] : '';
			$callback      = isset($filter[2]) ? $filter[2] : '';
			$priority      = isset($filter[3]) ? $filter[3] : 10;
			$accepted_args = isset($filter[4]) ? $filter[4] : 1;

			$this->add_filter($hook, $component, $callback, $priority, $accepted_args);
		}
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @since 1.0.0
	 * @access private
	 * @param array<int, array> $hooks            The collection of hooks that is being registered (that is, actions or filters).
	 * @param string            $hook             The name of the WordPress filter that is being registered.
	 * @param object            $component        A reference to the instance of the object on which the filter is defined.
	 * @param string            $callback         The name of the function definition on the $component.
	 * @param int               $priority         The priority at which the function should be fired.
	 * @param int               $accepted_args    The number of arguments that should be passed to the $callback.
	 * @return array<int, array> The collection of actions and filters registered with WordPress.
	 */
	private function add(array $hooks, string $hook, object $component, string $callback, int $priority, int $accepted_args): array {
		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);

		return $hooks;
	}

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function run(): void {
		foreach ($this->filters as $hook) {
			add_filter(
				$hook['hook'],
				array(
					$hook['component'],
					$hook['callback'],
				),
				$hook['priority'],
				$hook['accepted_args']
			);
		}

		foreach ($this->actions as $hook) {
			add_action(
				$hook['hook'],
				array(
					$hook['component'],
					$hook['callback'],
				),
				$hook['priority'],
				$hook['accepted_args']
			);
		}
	}
}
