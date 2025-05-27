<?php

declare(strict_types=1);

namespace WPPB\Providers;

use WPPB\Abstracts\AbstractServiceProvider;
use WPPB\Core\Container;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Core Service Provider
 *
 * Registers core plugin services like Hooker, Assets, and I18n.
 *
 * @since 1.0.0
 */
class CoreServiceProvider extends AbstractServiceProvider {

	/**
	 * Register services with the container
	 *
	 * @since 1.0.0
	 * @param Container $container The DI container
	 * @return void
	 */
	public function register( Container $container ): void {
		// Register Hooker as singleton
		$this->singleton(
			$container,
			'hooker',
			function ( Container $container ) {
				return new \WPPB\Core\Hooker();
			}
		);

		// Register Assets as singleton
		$this->singleton(
			$container,
			'assets',
			function ( Container $container ) {
				return new \WPPB\Core\Assets();
			}
		);

		// Register I18n as singleton
		$this->singleton(
			$container,
			'i18n',
			function ( Container $container ) {
				return new \WPPB\Core\I18n();
			}
		);

		// Register aliases for easier access
		$this->alias( $container, 'hooks', 'hooker' );
		$this->alias( $container, \WPPB\Core\Hooker::class, 'hooker' );
		$this->alias( $container, \WPPB\Core\Assets::class, 'assets' );
		$this->alias( $container, \WPPB\Core\I18n::class, 'i18n' );
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

		// Boot the hooker to register all hooks
		$hooker = $container->get( 'hooker' );
		if ( $hooker instanceof \WPPB\Core\Hooker ) {
			$hooker->run();
		}
	}
}
