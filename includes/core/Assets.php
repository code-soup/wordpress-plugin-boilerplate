<?php
/**
 * Assets class.
 *
 * @package WPPB
 */

declare( strict_types=1 );

namespace WPPB\Core;

/** If this file is called directly, abort. */
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
	 * A flag indicating if the environment is production.
	 *
	 * @var bool
	 */
	private bool $is_production;

	/**
	 * The transient key for the asset manifest.
	 *
	 * @var string
	 */
	private string $transient_key;

	/**
	 * The path to the manifest file.
	 *
	 * @var string
	 */
	private string $manifest_path;

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
		$this->plugin        = $plugin;
		$this->is_production = 'production' === $this->plugin->config['ENVIRONMENT'];
		$this->manifest_path = $this->plugin->config['PLUGIN_BASE_PATH'] . 'dist/manifest.json';
		$this->transient_key = sprintf(
			'%s_asset_manifest_%s',
			$this->plugin->config['PLUGIN_PREFIX'],
			$this->plugin->config['PLUGIN_VERSION']
		);
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
		if ( $this->is_production ) {
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

		if ( $this->is_production ) {
			$cached_manifest = get_transient( $this->transient_key );
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

		if ( ! $wp_filesystem->exists( $this->manifest_path ) ) {
			$this->manifest = array();
		} else {
			$manifest_contents = $wp_filesystem->get_contents( $this->manifest_path );
			$this->manifest    = json_decode( $manifest_contents, true ) ?? array();
		}

		if ( $this->is_production ) {
			set_transient( $this->transient_key, $this->manifest, WEEK_IN_SECONDS );
		}

		return $this->manifest;
	}
}
