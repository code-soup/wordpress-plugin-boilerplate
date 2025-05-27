<?php

declare(strict_types=1);

namespace WPPB\Traits;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Helper methods
 *
 * @since 1.0.0
 */
trait HelpersTrait {

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
		$base = dirname( __DIR__, 2 );

		return $this->join_path( $base, $path );
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
		$base = plugins_url( '/', dirname( __DIR__, 1 ) );

		return $this->join_path( $base, $path, '/' );
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
	 * @return string Plugin ID with optional appended string.
	 */
	private function get_plugin_id( string $append = '' ): string {
		$dashed = str_replace( '_', '-', $this->get_constant( 'PLUGIN_NAME' ) );

		return sanitize_title( $dashed ) . $append;
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
		$constants = \WPPB\Init::$constants;
		$name      = trim( strtoupper( $key ) );

		// Check if constant is defined first
		if ( ! isset( $constants[ $name ] ) ) {
			// Force string to avoid compiler errors
			$to_string = print_r( $name, true );

			// Log to error for debugging
			$this->log( "Invalid constant requested: $to_string" );

			// Exit
			return false;
		}

		// Return value by key
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
			$path = rtrim( $path, '/' );
			$path = ltrim( $path, '/' );
		}

		// Strip trailingslash just in case.
		$base = untrailingslashit( $base );
		$url  = array_filter( array( $base, $path ) );
		$url  = implode( $separator, $url );

		return untrailingslashit( $url );
	}

	/**
	 * Save something to WordPress debug.log
	 * Useful for debugging your code, this method will print_r any variable into log
	 *
	 * @since 1.0.0
	 * @param mixed $variable Variable to log.
	 * @return void
	 */
	private function log( $variable ): void {
		error_log( print_r( $variable, true ) );
	}
}
