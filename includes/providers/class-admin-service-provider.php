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
		parent::boot();

		if ( is_admin() && ! wp_doing_ajax() ) {
			// Register hook to initialize after WordPress is loaded
			add_action(
				'init',
				function () {
					$this->container->get( 'admin' )->init();
				}
			);
		}
	}
}
