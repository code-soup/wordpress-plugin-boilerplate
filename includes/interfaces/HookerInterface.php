<?php
/**
 * Hooker Interface.
 *
 * @package WPPB
 */

declare(strict_types=1);

namespace WPPB\Interfaces;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Hooker Interface.
 *
 * @package WPPB
 */
interface HookerInterface {
	/**
	 * Add a WordPress action.
	 *
	 * @param string   $hook          The name of the action to add.
	 * @param callable $callback      The callback to be executed.
	 * @param int      $priority      Optional. Used to specify the order in which the functions
	 *                                associated with a particular action are executed. Default 10.
	 * @param int      $accepted_args Optional. The number of arguments the function accepts. Default 1.
	 */
	public function add_action( string $hook, callable $callback, int $priority = 10, int $accepted_args = 1 ): void;

	/**
	 * Add a WordPress filter.
	 *
	 * @param string   $hook          The name of the filter to add.
	 * @param callable $callback      The callback to be executed.
	 * @param int      $priority      Optional. Used to specify the order in which the functions
	 *                                associated with a particular action are executed. Default 10.
	 * @param int      $accepted_args Optional. The number of arguments the function accepts. Default 1.
	 */
	public function add_filter( string $hook, callable $callback, int $priority = 10, int $accepted_args = 1 ): void;

	/**
	 * Add array of actions at once
	 *
	 * @since 1.0.0
	 * @param array<int, array> $actions Array of actions to add.
	 * @return void
	 */
	public function add_actions( array $actions = array() ): void;

	/**
	 * Add array of filters at once
	 *
	 * @since 1.0.0
	 * @param array<int, array> $filters Array of filters to add.
	 * @return void
	 */
	public function add_filters( array $filters = array() ): void;

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function run(): void;
}
