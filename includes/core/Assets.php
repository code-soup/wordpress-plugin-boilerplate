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
		if ( ! ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ) {
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

		$manifest_path = $this->plugin->config['PLUGIN_BASE_PATH'] . 'dist/manifest.json';

		if ( ! file_exists( $manifest_path ) ) {
			$this->manifest = array();
			return array();
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$this->manifest = json_decode( file_get_contents( $manifest_path ), true );

		return $this->manifest;
	}
}
