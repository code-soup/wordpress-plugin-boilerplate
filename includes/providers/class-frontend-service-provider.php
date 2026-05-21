<?php
/**
 * Frontend Service Provider.
 *
 * @package WPPB
 */

namespace WPPB\Providers;

use WPPB\Abstracts\AbstractServiceProvider;

/** If this file is called directly, abort. */
defined( 'ABSPATH' ) || die;

/**
 * The FrontendServiceProvider class.
 */
class FrontendServiceProvider extends AbstractServiceProvider {

	/**
	 * Register the service provider.
	 */
	public function register(): void {
		$this->container->singleton( 'frontend', \WPPB\Frontend\Init::class );
	}

	/**
	 * Boot the service provider.
	 */
	public function boot(): void {
		parent::boot();

		if ( ! is_admin() || wp_doing_ajax() ) {
			// Register hook to initialize after WordPress is loaded
			add_action(
				'init',
				function () {
					$this->container->get( 'frontend' )->init();
				}
			);
		}
	}
}
