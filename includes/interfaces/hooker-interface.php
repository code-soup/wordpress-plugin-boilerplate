<?php

namespace WPPB\Interfaces;

// Exit if accessed directly
defined( 'WPINC' ) || die;

/**
 * Interface for the Hooker class.
 *
 * Defines the methods that must be implemented by any class that handles
 * WordPress hooks (actions and filters).
 *
 * @since 1.0.0
 */
interface HookerInterface {
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
    public function add_action(string $hook, object $component, string $callback = '', int $priority = 10, int $accepted_args = 1): void;

    /**
     * Add array of actions at once
     *
     * @since 1.0.0
     * @param array<int, array> $actions Array of actions to add.
     * @return void
     */
    public function add_actions(array $actions = []): void;

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
    public function add_filter(string $hook, object $component, string $callback = '', int $priority = 10, int $accepted_args = 1): void;

    /**
     * Add array of filters at once
     *
     * @since 1.0.0
     * @param array<int, array> $filters Array of filters to add.
     * @return void
     */
    public function add_filters(array $filters = []): void;

    /**
     * Register the filters and actions with WordPress.
     *
     * @since 1.0.0
     * @return void
     */
    public function run(): void;
} 