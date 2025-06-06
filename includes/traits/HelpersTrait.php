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
	 * The instance.
	 *
	 * @var self
	 */
	protected static $instance;

	/**
	 * Get the instance.
	 *
	 * @return self
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get the plugin version.
	 *
	 * @return string
	 */
	public function get_version(): string {
		return '1.0.0';
	}

	/**
	 * Get the plugin name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return 'WordPress Plugin Boilerplate';
	}

	/**
	 * Get the plugin slug.
	 *
	 * @param bool $is_dashed Whether to return the slug as dashed.
	 * @return string
	 */
	public function get_slug( bool $is_dashed = false ): string {
		return $is_dashed ? 'wordpress-plugin-boilerplate' : 'wordpress_plugin_boilerplate';
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function get_path(): string {
		return plugin_dir_path( __DIR__ );
	}

	/**
	 * Get the plugin URL.
	 *
	 * @return string
	 */
	public function get_url(): string {
		return plugin_dir_url( __DIR__ );
	}

	/**
	 * Get the plugin basename.
	 *
	 * @return string
	 */
	public function get_basename(): string {
		return plugin_basename( __DIR__ );
	}

	/**
	 * Return absolute path to plugin dir
	 * Always returns path without trailing slash
	 *
	 * @since 1.0.0
	 * @param string $path Optional path to append.
	 * @return string Absolute path to plugin directory.
	 */
	private function get_plugin_dir_path( string $path = '' ): string {
		// Force baseurl to be plugin root directory.
		$base      = $this->get_constant( 'PLUGIN_BASE_PATH' );
		$base      = apply_filters( $this->get_plugin_id( '_plugin_dir_path', false ), $base );
		$full_path = $this->join_path( $base, $path );

		/**
		 * Filter the plugin directory path
		 *
		 * @since 1.0.0
		 * @param string $full_path The full plugin directory path.
		 * @param string $path      The appended path.
		 * @param string $base      The base plugin directory.
		 */
		return $full_path;
	}

	/**
	 * Return plugin directory URL
	 * Always returns URL without trailing slash
	 *
	 * @since 1.0.0
	 * @param string $path Optional path to append.
	 * @return string Plugin directory URL.
	 */
	private function get_plugin_dir_url( string $path = '' ): string {
		// Force baseurl to be plugin root directory.
		$base     = plugins_url( DIRECTORY_SEPARATOR . basename( $this->get_constant( 'PLUGIN_BASE_PATH' ) ) );
		$base     = apply_filters( $this->get_plugin_id( '_plugin_dir_url', false ), $base );
		$full_url = $this->join_path( $base, $path );

		/**
		 * Filter the plugin directory URL
		 *
		 * @since 1.0.0
		 * @param string $full_url The full plugin directory URL.
		 * @param string $path     The appended path.
		 * @param string $base     The base plugin directory URL.
		 */
		return $full_url;
	}

	/**
	 * Returns PLUGIN_NAME constant
	 *
	 * @since 1.0.0
	 * @return string Plugin name.
	 */
	private function get_plugin_name(): string {
		return $this->get_constant( 'PLUGIN_NAME' );
	}

	/**
	 * Returns PLUGIN_VERSION constant
	 *
	 * @since 1.0.0
	 * @return string Plugin version.
	 */
	private function get_plugin_version(): string {
		return $this->get_constant( 'PLUGIN_VERSION' );
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
	private function get_plugin_id( string $append = '', bool $is_dashed = true ): string {
		$dashed = sanitize_title( $this->get_constant( 'PLUGIN_NAME' ) . $append );

		if ( $is_dashed ) {
			return $dashed;
		}

		return str_replace( '-', '_', $dashed );
	}

	/**
	 * Get plugin constant by name
	 *
	 * @since 1.0.0
	 * @param string $key Constant name.
	 * @return string|false Constant value or false if not found.
	 * @throws \Exception If constant is not defined.
	 */
	private function get_constant( string $key ) {
		$constants = \WPPB\Core\Init::$constants;
		$name      = trim( strtoupper( $key ) );

		// Check if constant is defined first.
		if ( ! isset( $constants[ $name ] ) ) {

			// Exit.
			return false;
		}

		// Return value by key.
		return $constants[ $name ];
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
	 * Sanitize a string.
	 *
	 * @param string $value The string to sanitize.
	 * @return string
	 */
	public function sanitize_string( string $value ): string {
		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize an array.
	 *
	 * @param array $value The array to sanitize.
	 * @return array
	 */
	public function sanitize_array( array $value ): array {
		return array_map( array( $this, 'sanitize_string' ), $value );
	}

	/**
	 * Sanitize an email.
	 *
	 * @param string $value The email to sanitize.
	 * @return string
	 */
	public function sanitize_email( string $value ): string {
		return sanitize_email( $value );
	}

	/**
	 * Sanitize a URL.
	 *
	 * @param string $value The URL to sanitize.
	 * @return string
	 */
	public function sanitize_url( string $value ): string {
		return esc_url_raw( $value );
	}

	/**
	 * Sanitize a file name.
	 *
	 * @param string $value The file name to sanitize.
	 * @return string
	 */
	public function sanitize_file_name( string $value ): string {
		return sanitize_file_name( $value );
	}

	/**
	 * Sanitize a class name.
	 *
	 * @param string $value The class name to sanitize.
	 * @return string
	 */
	public function sanitize_class_name( string $value ): string {
		return sanitize_html_class( $value );
	}

	/**
	 * Sanitize a key.
	 *
	 * @param string $value The key to sanitize.
	 * @return string
	 */
	public function sanitize_key( string $value ): string {
		return sanitize_key( $value );
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
