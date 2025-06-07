<?php
/**
 * Core Service Provider.
 *
 * @package WPPB
 */

namespace WPPB\Providers;

use WPPB\Abstracts\AbstractServiceProvider;

/**
 * If this file is called directly, abort.
 */
defined( 'ABSPATH' ) || die;

/**
 * Core Service Provider
 *
 * This provider is reserved for registering core services.
 * You can add your own service providers for your plugin's features.
 *
 * @since 1.0.0
 */
class CoreServiceProvider extends AbstractServiceProvider {

	/**
	 * The provided services.
	 *
	 * @var array
	 */
	protected array $provides = array();

	/**
	 * Register services with the container.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register(): void {
		// Core services are now registered directly in the Plugin class.
		// You can add your own service bindings here.
	}
}
