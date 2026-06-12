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
		
		$this->manifest_path = sprintf(
			'%sdist/manifest.json',
			$this->plugin->config['PLUGIN_BASE_PATH']
		);

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

		// Validate path - prevent directory traversal.
		$path = str_replace( array( '..', '\\' ), '', $path );

		return sprintf(
			'%sdist/%s',
			$this->plugin->config['PLUGIN_URL'],
			$path
		);
	}

	/**
	 * Check if an asset exists.
	 *
	 * @param string $asset_key The manifest key of the asset.
	 * @return bool True if asset exists, false otherwise.
	 */
	public function asset_exists( string $asset_key ): bool {
		
		if ( $this->is_production ) {
			$manifest = $this->get_manifest();
			return isset( $manifest[ $asset_key ] );
		}

		// In development, check if file exists in dist directory.
		$file_path = sprintf(
			'%sdist/%s',
			$this->plugin->config['PLUGIN_BASE_PATH'],
			$asset_key
		);

		return file_exists( $file_path );
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

		if ( $this->is_production )
		{
			// Include file modification time in cache key to bust cache on deploy.
			$manifest_mtime = file_exists( $this->manifest_path )
				? filemtime( $this->manifest_path )
				: 0;
			$cache_key      = $this->transient_key . '_' . $manifest_mtime;

			$cached_manifest = get_transient( $cache_key );

			if ( false !== $cached_manifest && is_array( $cached_manifest ) )
			{
				$this->manifest = $cached_manifest;
				return $this->manifest;
			}
		}

		$this->init_filesystem();

		if ( $this->has_filesystem() )
		{
			global $wp_filesystem;

			if ( ! $wp_filesystem->exists( $this->manifest_path ) )
			{
				$this->manifest = array();
			}
			else {
				$manifest_contents = $wp_filesystem->get_contents( $this->manifest_path );
				$this->manifest    = json_decode( $manifest_contents, true ) ?? array();
			}
		} else {
			// Fallback to direct file access.
			if ( file_exists( $this->manifest_path ) )
			{
				$manifest_contents = file_get_contents( $this->manifest_path );
				$this->manifest    = json_decode( $manifest_contents, true ) ?? array();
			}
			else {
				$this->manifest = array();
			}
		}

		if ( $this->is_production ) {
			set_transient( $cache_key, $this->manifest, WEEK_IN_SECONDS );
		}

		return $this->manifest;
	}

	/**
	 * Initialize WordPress filesystem.
	 */
	private function init_filesystem(): void {
		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			\WP_Filesystem();
		}
	}

	/**
	 * Check if WP_Filesystem is available.
	 *
	 * @return bool
	 */
	private function has_filesystem(): bool {
		global $wp_filesystem;
		return ! empty( $wp_filesystem );
	}
}
