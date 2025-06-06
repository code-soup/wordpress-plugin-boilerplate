<?php
/**
 * Hooker class for managing WordPress hooks.
 *
 * @package WPPB
 */

namespace WPPB\Core;

use WPPB\Interfaces\HookerInterface;

/**
 * If this file is called directly, abort.
 */
defined( 'ABSPATH' ) || die;

/**
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
	protected array $actions = array();

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var array<int, array> The filters registered with WordPress to fire when the plugin loads.
	 */
	protected array $filters = array();

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Arrays are already initialized with typed properties.
	}

	/**
	 * Add a WordPress action.
	 *
	 * @param string   $hook          The name of the action to add.
	 * @param callable $callback      The callback to be executed.
	 * @param int      $priority      Optional. Used to specify the order in which the functions
	 *                                associated with a particular action are executed. Default 10.
	 * @param int      $accepted_args Optional. The number of arguments the function accepts. Default 1.
	 */
	public function add_action( string $hook, callable $callback, int $priority = 10, int $accepted_args = 1 ): void {
		add_action( $hook, $callback, $priority, $accepted_args );
	}

	/**
	 * Add array of actions at once
	 *
	 * @since 1.0.0
	 * @param array<int, array> $actions Array of actions to add.
	 * @return void
	 */
	public function add_actions( array $actions = array() ): void {
		foreach ( $actions as $action ) {
			$hook          = isset( $action[0] ) ? $action[0] : '';
			$component     = isset( $action[1] ) ? $action[1] : '';
			$callback      = isset( $action[2] ) ? $action[2] : '';
			$priority      = isset( $action[3] ) ? $action[3] : 10;
			$accepted_args = isset( $action[4] ) ? $action[4] : 1;

			$this->add_action( $hook, $component, $priority, $accepted_args );
		}
	}

	/**
	 * Add a WordPress filter.
	 *
	 * @param string   $hook          The name of the filter to add.
	 * @param callable $callback      The callback to be executed.
	 * @param int      $priority      Optional. Used to specify the order in which the functions
	 *                                associated with a particular action are executed. Default 10.
	 * @param int      $accepted_args Optional. The number of arguments the function accepts. Default 1.
	 */
	public function add_filter( string $hook, callable $callback, int $priority = 10, int $accepted_args = 1 ): void {
		add_filter( $hook, $callback, $priority, $accepted_args );
	}

	/**
	 * Add array of filters at once
	 *
	 * @since 1.0.0
	 * @param array<int, array> $filters Array of filters to add.
	 * @return void
	 */
	public function add_filters( array $filters = array() ): void {
		foreach ( $filters as $filter ) {
			$hook          = isset( $filter[0] ) ? $filter[0] : '';
			$component     = isset( $filter[1] ) ? $filter[1] : '';
			$callback      = isset( $filter[2] ) ? $filter[2] : '';
			$priority      = isset( $filter[3] ) ? $filter[3] : 10;
			$accepted_args = isset( $filter[4] ) ? $filter[4] : 1;

			$this->add_filter( $hook, $component, $priority, $accepted_args );
		}
	}

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function run(): void {
		foreach ( $this->filters as $hook ) {
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

		foreach ( $this->actions as $hook ) {
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
