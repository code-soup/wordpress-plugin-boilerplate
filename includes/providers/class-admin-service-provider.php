<?php
/**
 * Admin Service Provider.
 *
 * @package WPPB
 */

declare( strict_types=1 );

namespace WPPB\Providers;

defined( 'ABSPATH' ) || die;

use WPPB\Abstracts\AbstractServiceProvider;
use WPPB\Admin\Init as AdminInit;
use WPPB\Core\Container;
use WPPB\Core\Plugin;

/**
 * The admin service provider.
 */
class AdminServiceProvider extends AbstractServiceProvider {

	/**
	 * Register the service provider.
	 */
	public function register(): void {
		$this->container->singleton(
			'admin',
			function ( Container $container ): AdminInit {
				/** @var Plugin $plugin */
				$plugin = $container->get( Plugin::class );

				return new AdminInit(
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

		if ( is_admin() && ! wp_doing_ajax() ) {
			$this->container->get( 'admin' )->init();
		}
	}
}