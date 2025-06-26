<?php
/**
 * Admin Service Provider.
 *
 * @package WPPB
 */

namespace WPPB\Providers;

use WPPB\Abstracts\AbstractServiceProvider;
use WPPB\Admin\Init as AdminInit;

/**
 * The admin service provider.
 */
class AdminServiceProvider extends AbstractServiceProvider {

	/**
	 * Register the service provider.
	 */
	public function register(): void {
		$this->container->singleton( 'admin', \WPPB\Admin\Init::class );
	}

	/**
	 * Boot the service provider.
	 */
	public function boot(): void {
		if ( is_admin() ) {
			$this->container->get( 'admin' );
		}
	}
}
