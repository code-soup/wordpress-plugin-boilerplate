<?php

declare(strict_types=1);

namespace WPPB\Providers;

use WPPB\Abstracts\AbstractServiceProvider;
use WPPB\Core\Container;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Admin Service Provider
 *
 * Registers admin-specific services and functionality.
 *
 * @since 1.0.0
 */
class AdminServiceProvider extends AbstractServiceProvider {

	/**
	 * Register services with the container
	 *
	 * @since 1.0.0
	 * @param Container $container The DI container
	 * @return void
	 */
	public function register( Container $container ): void {
		// Only register admin services if we're in admin
		if ( ! is_admin() ) {
			return;
		}

		// Register Admin Init as singleton
		$this->singleton(
			$container,
			'admin.init',
			function ( Container $container ) {
				return new \WPPB\Admin\Init();
			}
		);

		// Register aliases
		$this->alias( $container, 'admin', 'admin.init' );
		$this->alias( $container, \WPPB\Admin\Init::class, 'admin.init' );
	}

	/**
	 * Boot services after all providers have been registered
	 *
	 * @since 1.0.0
	 * @param Container $container The DI container
	 * @return void
	 */
	public function boot( Container $container ): void {
		parent::boot( $container );

		// Admin services are automatically booted when instantiated
		// since they register their hooks in the constructor
	}
}
