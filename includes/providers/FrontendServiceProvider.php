<?php

declare(strict_types=1);

namespace WPPB\Providers;

use WPPB\Abstracts\AbstractServiceProvider;
use WPPB\Core\Container;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Frontend Service Provider
 *
 * Registers frontend-specific services and functionality.
 *
 * @since 1.0.0
 */
class FrontendServiceProvider extends AbstractServiceProvider {

	/**
	 * Register services with the container
	 *
	 * @since 1.0.0
	 * @param Container $container The DI container
	 * @return void
	 */
	public function register( Container $container ): void {
		// Only register frontend services if we're not in admin
		if ( is_admin() ) {
			return;
		}

		// Register Frontend Init as singleton
		$this->singleton(
			$container,
			'frontend.init',
			function ( Container $container ) {
				return new \WPPB\Frontend\Init();
			}
		);

		// Register aliases
		$this->alias( $container, 'frontend', 'frontend.init' );
		$this->alias( $container, \WPPB\Frontend\Init::class, 'frontend.init' );
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

		// Frontend services are automatically booted when instantiated
		// since they register their hooks in the constructor
	}
}
