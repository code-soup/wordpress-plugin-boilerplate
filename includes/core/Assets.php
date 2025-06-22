<?php
/**
 * Assets class.
 *
 * @package WPPB
 */

declare(strict_types=1);

namespace WPPB\Core;

/**
 * If this file is called directly, abort.
 */
defined( 'ABSPATH' ) || die;

/**
 * Handles the registration and enqueueing of plugin assets.
 */
final class Assets {

	/**
	 * The plugin instance.
	 *
	 * @var Plugin
	 */
	private Plugin $plugin;

	/**
	 * The cached webpack manifest data.
	 *
	 * @var array<string, mixed>|null
	 */
	private ?array $manifest = null;

	/**
	 * Constructor.
	 *
	 * @param Plugin $plugin The main plugin instance.
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Get the full URL for a given asset.
	 *
	 * This method handles mapping the asset name to its final, hashed URL
	 * using the webpack manifest file in production.
	 *
	 * @param string $asset_key The manifest key of the asset (e.g., 'common.js').
	 * @return string The full URL to the asset.
	 */
	public function get_asset_url( string $asset_key ): string {
		// In development, the asset key is the filename.
		$path = $asset_key;

		// In production, get the hashed filename from the manifest.
		if ( 'production' === $this->plugin->config['ENVIRONMENT'] ) {
			$manifest = $this->get_manifest();
			$path     = $manifest[ $asset_key ] ?? $asset_key;
		}

		return $this->plugin->config['PLUGIN_URL'] . 'dist/' . $path;
	}

	/**
	 * Get the manifest data, caching it for subsequent calls.
	 *
	 * @return array<string, mixed>
	 */
	private function get_manifest(): array {
		if ( ! is_null( $this->manifest ) ) {
			return $this->manifest;
		}

		$is_production = 'production' === $this->plugin->config['ENVIRONMENT'];
		$transient_key = $this->plugin->config['PLUGIN_PREFIX'] . '_asset_manifest';

		if ( $is_production ) {
			$cached_manifest = get_transient( $transient_key );
			if ( false !== $cached_manifest && is_array( $cached_manifest ) ) {
				$this->manifest = $cached_manifest;
				return $this->manifest;
			}
		}

		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			\WP_Filesystem();
		}

		$manifest_path = $this->plugin->config['PLUGIN_BASE_PATH'] . 'dist/manifest.json';

		if ( ! $wp_filesystem->exists( $manifest_path ) ) {
			$this->manifest = array();
		} else {
			$manifest_contents = $wp_filesystem->get_contents( $manifest_path );
			$this->manifest    = json_decode( $manifest_contents, true ) ?? array();
		}

		if ( $is_production ) {
			set_transient( $transient_key, $this->manifest, YEAR_IN_SECONDS );
		}

		return $this->manifest;
	}
}
