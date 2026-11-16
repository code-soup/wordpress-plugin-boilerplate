<?php
/**
 * Frontend Service Provider.
 *
 * @package WPPB
 */

declare( strict_types=1 );

namespace WPPB\Providers;

defined( 'ABSPATH' ) || die;

use WPPB\Abstracts\AbstractServiceProvider;
use WPPB\Core\Container;
use WPPB\Core\Plugin;
use WPPB\Frontend\Init as FrontendInit;

/**
 * The FrontendServiceProvider class.
 */
class FrontendServiceProvider extends AbstractServiceProvider {

	/**
	 * Register the service provider.
	 */
	public function register(): void {
		$this->container->singleton(
			'frontend',
			function ( Container $container ): FrontendInit {
				/** @var Plugin $plugin */
				$plugin = $container->get( Plugin::class );

				return new FrontendInit(
					$container->get( 'hooker' ),
					$container->get( 'assets' ),
					(string) $plugin->config['PLUGIN_VERSION']
				);
			}
		);
	}

	/**
	 * Boot the service provider.
	 */
	public function boot(): void {
		parent::boot();

		if ( ! is_admin() || wp_doing_ajax() ) {
			$this->container->get( 'frontend' )->init();
		}
	}
}