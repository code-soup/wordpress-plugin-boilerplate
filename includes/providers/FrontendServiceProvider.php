<?php
/**
 * Frontend Service Provider.
 *
 * @package WPPB
 */

namespace WPPB\Providers;

use WPPB\Abstracts\AbstractServiceProvider;
use WPPB\Frontend\Frontend;

/**
 * If this file is called directly, abort.
 */
defined( 'ABSPATH' ) || die;

/**
 * The FrontendServiceProvider class.
 */
class FrontendServiceProvider extends AbstractServiceProvider {


	/**
	 * The provided services.
	 *
	 * @var array
	 */
	protected array $provides = array(
		'frontend',
	);

	/**
	 * Register the service provider.
	 */
	public function register(): void {
		$this->register_service(
			'frontend',
			function () {
				return new Frontend();
			}
		);
	}

	/**
	 * Boot the service provider.
	 */
	public function boot(): void {
		if ( ! is_admin() ) {
			$this->container->set( 'frontend_init', new \WPPB\Frontend\Init() );
		}
	}
}
