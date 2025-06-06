<?php
/**
 * Core Service Provider.
 *
 * @package WPPB
 */

namespace WPPB\Providers;

use WPPB\Abstracts\AbstractServiceProvider;
use WPPB\Core\Assets;
use WPPB\Core\Hooker;
use WPPB\Core\I18n;
use WPPB\Core\Lifecycle;
use WPPB\Core\Core;

/**
 * If this file is called directly, abort.
 */
defined( 'ABSPATH' ) || die;

/**
 * Core Service Provider
 *
 * Registers core plugin services like Hooker, Assets, and I18n.
 *
 * @since 1.0.0
 */
class CoreServiceProvider extends AbstractServiceProvider {

	/**
	 * The provided services.
	 *
	 * @var array
	 */
	protected array $provides = array(
		'core',
	);

	/**
	 * Register services with the container
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register(): void {
		$this->register_service(
			'core',
			function () {
				return new Core();
			}
		);
	}

	/**
	 * Boot services after all providers have been registered
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function boot(): void {
		$this->container->set( 'lifecycle', new Lifecycle() );
		$this->container->set( 'hooker', new Hooker() );
		$this->container->set( 'assets', new Assets( WPPB_PLUGIN_BASE_DIR . '/dist/manifest.json' ) );
		$this->container->set( 'i18n', new I18n( 'WPPB' ) );
	}
}
