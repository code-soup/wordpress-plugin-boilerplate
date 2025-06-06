<?php
/**
 * Assets Interface.
 *
 * @package WPPB
 */

namespace WPPB\Interfaces;

/**
 * If this file is called directly, abort.
 */
defined( 'ABSPATH' ) || die;

/**
 * The AssetsInterface interface.
 */
interface AssetsInterface {
	/**
	 * Get the URL of an asset.
	 *
	 * @param string $asset The name of the asset.
	 * @return string The URL of the asset.
	 */
	public function get_asset_url( string $asset ): string;

	/**
	 * Enqueue scripts.
	 */
	public function enqueue_scripts(): void;

	/**
	 * Enqueue styles.
	 */
	public function enqueue_styles(): void;
}
