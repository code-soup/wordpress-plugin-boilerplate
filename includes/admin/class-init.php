<?php
/**
 * Admin Init class.
 *
 * @package WPPB
 */

declare( strict_types=1 );

namespace WPPB\Admin;

use WPPB\Core\Assets;
use WPPB\Core\Hooker;

defined( 'ABSPATH' ) || die;

/**
 * The admin-specific functionality of the plugin.
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
				array( 'admin_enqueue_scripts', $this ),
			)
		);
	}

	/**
	 * Enqueue the admin styles.
	 */
	public function admin_enqueue_scripts(): void {

		$dependencies = array();

		if ( $this->assets->asset_exists( 'runtime.js' ) ) {
			wp_enqueue_script(
				'wppb-admin-runtime',
				$this->assets->get_asset_url( 'runtime.js' ),
				array(),
				$this->plugin_version,
				true
			);
			$dependencies[] = 'wppb-admin-runtime';
		}

		if ( $this->assets->asset_exists( 'vendor-libs.js' ) ) {
			wp_enqueue_script(
				'wppb-admin-vendor',
				$this->assets->get_asset_url( 'vendor-libs.js' ),
				$dependencies,
				$this->plugin_version,
				true
			);
			$dependencies[] = 'wppb-admin-vendor';
		}

		if ( $this->assets->asset_exists( 'admin-common.css' ) ) {
			wp_enqueue_style(
				'wppb-admin',
				$this->assets->get_asset_url( 'admin-common.css' ),
				array(),
				$this->plugin_version
			);
		}

		if ( $this->assets->asset_exists( 'admin-common.js' ) ) {
			wp_enqueue_script(
				'wppb-admin-common',
				$this->assets->get_asset_url( 'admin-common.js' ),
				$dependencies,
				$this->plugin_version,
				true
			);
		}
	}
}