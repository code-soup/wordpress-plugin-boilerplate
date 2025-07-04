<?php
/**
 * Helpers trait.
 *
 * @package WPPB
 */

declare( strict_types=1 );

namespace WPPB\Traits;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Helper methods
 *
 * @since 1.0.0
 */
trait HelpersTrait {

	/**
	 * Get the plugin name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->config['PLUGIN_NAME'];
	}

	/**
	 * Get the plugin version.
	 *
	 * @return string
	 */
	public function get_version(): string {
		return $this->config['PLUGIN_VERSION'];
	}

	/**
	 * Get the plugin prefix.
	 *
	 * @return string
	 */
	public function get_prefix(): string {
		return $this->config['PLUGIN_PREFIX'];
	}

	/**
	 * Get the plugin base path.
	 *
	 * @return string
	 */
	public function get_base_path(): string {
		return $this->config['PLUGIN_BASE_PATH'];
	}

	/**
	 * Get the plugin URL.
	 *
	 * @return string
	 */
	public function get_url(): string {
		return $this->config['PLUGIN_URL'];
	}

	/**
	 * Get the plugin basename.
	 *
	 * @return string
	 */
	public function get_basename(): string {
		return $this->config['PLUGIN_BASENAME'];
	}

	/**
	 * Get the plugin text domain.
	 *
	 * @return string
	 */
	public function get_text_domain(): string {
		return $this->config['PLUGIN_TEXTDOMAIN'];
	}

	/**
	 * Returns PLUGIN_PREFIX constant as ID
	 * Converts to-slug-like-id
	 * and appends additional text at the end for custom unique id
	 *
	 * @since 1.0.0
	 * @param string $append Optional string to append to the ID.
	 * @param bool   $is_dashed Whether to return dashed or underscored string.
	 * @return string Plugin ID with optional appended string.
	 */
	public function get_plugin_id( string $append = '', bool $is_dashed = true ): string {
		$dashed = sanitize_title( $this->get_name() . $append );

		if ( $is_dashed ) {
			return $dashed;
		}

		return str_replace( '-', '_', $dashed );
	}

	/**
	 * Join two paths into single absolute path or URL
	 *
	 * @since 1.0.0
	 * @param string $base Base location.
	 * @param string $path Path to append.
	 * @param string $separator Directory separator to use.
	 * @return string Combined path.
	 */
	private function join_path( string $base = '', string $path = '', string $separator = DIRECTORY_SEPARATOR ): string {
		// Strip slashes on both ends.
		if ( $path ) {
			$path = trim( $path, $separator );
		}

		// Strip trailingslash just in case.
		$base = untrailingslashit( $base );
		$url  = array_filter( array( $base, $path ) );
		$url  = implode( $separator, $url );

		return untrailingslashit( $url );
	}

	/**
	 * Convert a string to camel case.
	 *
	 * @param string $input_string The string to convert.
	 * @param bool   $is_dashed Whether the string is dashed.
	 * @return string
	 */
	public function to_camel_case( string $input_string, bool $is_dashed = false ): string {
		if ( $is_dashed ) {
			$input_string = str_replace( '-', '_', $input_string );
		}
		return lcfirst( str_replace( ' ', '', ucwords( str_replace( '_', ' ', $input_string ) ) ) );
	}
}
