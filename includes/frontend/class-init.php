<?php
/**
 * Frontend Init Class.
 *
 * @package WPPB
 */

declare( strict_types=1 );

namespace WPPB\Frontend;

use WPPB\Core\Assets;
use WPPB\Core\Hooker;

defined( 'ABSPATH' ) || die;

/**
 * The public-facing functionality of the plugin.
 */
class Init {

	/**
	 * @var Hooker
	 */
	private Hooker $hooker;

	/**
	 * @var Assets
	 */
	private Assets $assets;

	/**
	 * @var string
	 */
	private string $plugin_version;

	/**
	 * @param Hooker $hooker          Hooker service.
	 * @param Assets $assets          Assets service.
	 * @param string $plugin_version  Plugin version for cache busting.
	 */
	public function __construct( Hooker $hooker, Assets $assets, string $plugin_version ) {
		$this->hooker         = $hooker;
		$this->assets         = $assets;
		$this->plugin_version = $plugin_version;
	}

	/**
	 * Initialize and register hooks.
	 */
	public function init(): void {
		$this->hooker->add_actions(
			array(
				array( 'wp_enqueue_scripts', $this ),
			)
		);
	}

	/**
	 * Enqueue the frontend styles.
	 */
	public function wp_enqueue_scripts(): void {

		$dependencies = array();

		if ( $this->assets->asset_exists( 'runtime.js' ) ) {
			wp_enqueue_script(
				'wppb-frontend-runtime',
				$this->assets->get_asset_url( 'runtime.js' ),
				array(),
				$this->plugin_version,
				true
			);
			$dependencies[] = 'wppb-frontend-runtime';
		}

		if ( $this->assets->asset_exists( 'vendor-libs.js' ) ) {
			wp_enqueue_script(
				'wppb-frontend-vendor',
				$this->assets->get_asset_url( 'vendor-libs.js' ),
				$dependencies,
				$this->plugin_version,
				true
			);
			$dependencies[] = 'wppb-frontend-vendor';
		}

		if ( $this->assets->asset_exists( 'frontend-common.css' ) ) {
			wp_enqueue_style(
				'wppb-frontend',
				$this->assets->get_asset_url( 'frontend-common.css' ),
				array(),
				$this->plugin_version
			);
		}

		if ( $this->assets->asset_exists( 'frontend-common.js' ) ) {
			wp_enqueue_script(
				'wppb-frontend-common',
				$this->assets->get_asset_url( 'frontend-common.js' ),
				$dependencies,
				$this->plugin_version,
				true
			);
		}
	}
}