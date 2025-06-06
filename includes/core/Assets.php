<?php
/**
 * Assets class for managing scripts and styles.
 *
 * @package WPPB
 */

namespace WPPB\Core;

use WPPB\Interfaces\AssetsInterface;

/**
 * If this file is called directly, abort.
 */
defined( 'ABSPATH' ) || die;

/**
 * Get paths for assets.
 *
 * @since 1.0.0
 */
class Assets implements AssetsInterface {

	use \WPPB\Traits\HelpersTrait;

	/**
	 * The manifest file path.
	 *
	 * @var string
	 */
	private $manifest_path;

	/**
	 * The manifest data.
	 *
	 * @var array
	 */
	private $manifest;

	/**
	 * URI to theme 'dist' folder
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private string $dist_uri;

	/**
	 * Assets constructor.
	 */
	public function __construct() {
		$this->dist_uri = $this->get_plugin_dir_url( 'dist' );
	}

	/**
	 * Initialize the assets.
	 */
	public function init(): void {
		$this->load_manifest();
	}

	/**
	 * Load the asset manifest.
	 *
	 * @throws \Exception If the manifest file cannot be loaded or decoded.
	 */
	private function load_manifest(): void {
		if ( ! file_exists( $this->manifest_path ) ) {
			throw new \Exception( 'Asset manifest not found.' );
		}

		$manifest_json = file_get_contents( $this->manifest_path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$manifest      = json_decode( $manifest_json, true );

		if ( json_last_error() !== JSON_ERROR_NONE ) {
			throw new \Exception( 'Error decoding asset manifest: ' . esc_html( json_last_error_msg() ) );
		}

		$this->manifest = $manifest;
	}

	/**
	 * Get the URL of an asset from the manifest.
	 *
	 * @param string $asset The name of the asset.
	 *
	 * @return string The asset URL.
	 * @throws \Exception If the asset is not found in the manifest.
	 */
	public function get_asset_url( string $asset ): string {
		if ( ! isset( $this->manifest[ $asset ] ) ) {
			throw new \Exception( esc_html( "Asset '{$asset}' not found in manifest." ) );
		}
		return $this->manifest[ $asset ];
	}

	/**
	 * Get the path of an asset from the manifest.
	 *
	 * @param string $asset The name of the asset.
	 *
	 * @return string The asset path.
	 */
	public function get_asset_path( string $asset ): string {
		return str_replace( home_url(), ABSPATH, $this->get_asset_url( $asset ) );
	}

	/**
	 * Get full URI to single asset
	 *
	 * @since 1.0.0
	 * @param  string $filename File name.
	 * @return string           URI to resource
	 */
	public function get( string $filename = '' ): string {
		return $this->locate( $filename );
	}

	/**
	 * Fix URL for requested files
	 *
	 * @since 1.0.0
	 * @param  string $filename Requested asset.
	 * @return string           URL to the asset
	 */
	private function locate( string $filename = '' ): string {
		// Return URL to requested file from manifest.
		if ( array_key_exists( $filename, $this->manifest ) ) {
			return $this->join_path( $this->dist_uri, $this->manifest[ $filename ] );
		}

		switch ( pathinfo( $filename, PATHINFO_EXTENSION ) ) {
			case 'js':
				$filename = $this->join_path( 'scripts', $filename );
				break;

			case 'css':
				$filename = $this->join_path( 'styles', $filename );
				break;
		}

		// Return default file location.
		return $this->join_path( $this->dist_uri, $filename );
	}
}
