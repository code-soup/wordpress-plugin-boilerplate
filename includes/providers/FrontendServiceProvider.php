<?php
/**
 * Frontend Service Provider.
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
 * The FrontendServiceProvider class.
 */
class FrontendServiceProvider extends AbstractServiceProvider {

	/**
	 * Register the service provider.
	 */
	public function register(): void {
		// Intentionally empty.
	}

	/**
	 * Boot the service provider.
	 */
	public function boot(): void {
		if ( ! is_admin() ) {
			$this->container->singleton( 'frontend_init', \WPPB\Frontend\Init::class );
		}
	}
}
