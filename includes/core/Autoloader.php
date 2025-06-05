<?php

declare(strict_types=1);

namespace WPPB\Core;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * PSR-4 Autoloader
 *
 * This autoloader provides PSR-4 compatibility.
 * Class names are expected to match filenames directly (e.g., class My_Class in My_Class.php).
 *
 * @since 1.0.0
 */
class Autoloader {

	/**
	 * Namespace to directory mappings
	 *
	 * @var array<string, string>
	 * @since 1.0.0
	 */
	private array $namespace_map = array();

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->setup_namespace_mappings();
	}

	/**
	 * Register the autoloader
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register(): void {
		spl_autoload_register( array( $this, 'load_class' ) );
	}

	/**
	 * Unregister the autoloader
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function unregister(): void {
		spl_autoload_unregister( array( $this, 'load_class' ) );
	}

	/**
	 * Load a class file based on PSR-4 standards
	 *
	 * @since 1.0.0
	 * @param string $class_name The fully qualified class name
	 * @return bool True if the file was loaded, false otherwise
	 */
	public function load_class( string $class_name ): bool {
		// Normalize the class name
		$class_name = ltrim( $class_name, '\\' );

		// Find the matching namespace
		$namespace_prefix = $this->find_namespace_prefix( $class_name );
		if ( ! $namespace_prefix ) {
			return false;
		}

		// Get the base directory for this namespace
		$base_directory = $this->namespace_map[ $namespace_prefix ];

		// Remove the namespace prefix from the class name to get the relative class path
		$relative_class = substr( $class_name, strlen( $namespace_prefix ) );

		// Convert namespace separators in the relative class name to directory separators,
		// and append .php extension.
		// Example: My_Namespace\Sub_Dir\My_Class -> My_Namespace/Sub_Dir/My_Class.php
		$file_path_segment = str_replace( '\\', DIRECTORY_SEPARATOR, $relative_class );
		$file_path         = $base_directory . DIRECTORY_SEPARATOR . $file_path_segment . '.php';

		// Normalize the path
		$file_path = $this->normalize_path( $file_path );

		// Load the file if it exists
		if ( $this->load_file( $file_path ) ) {
			return true;
		}

		// For strict PSR-4, we don't try alternative locations.
		return false;
	}

	/**
	 * Setup namespace to directory mappings
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function setup_namespace_mappings(): void {
		$plugin_dir = dirname( dirname( __DIR__ ) );

		$this->namespace_map = array(
			'WPPB\\Core\\'       => $plugin_dir . '/includes/core',
			'WPPB\\Admin\\'      => $plugin_dir . '/includes/admin',
			'WPPB\\Frontend\\'   => $plugin_dir . '/includes/frontend',
			'WPPB\\Providers\\'  => $plugin_dir . '/includes/providers',
			'WPPB\\Abstracts\\'  => $plugin_dir . '/includes/abstracts',
			'WPPB\\Interfaces\\' => $plugin_dir . '/includes/interfaces',
			'WPPB\\Traits\\'     => $plugin_dir . '/includes/traits',
			'WPPB\\'             => $plugin_dir . '/includes',
		);
	}

	/**
	 * Find the namespace prefix for a class
	 *
	 * @since 1.0.0
	 * @param string $class_name The fully qualified class name
	 * @return string|false The namespace prefix or false if not found
	 */
	private function find_namespace_prefix( string $class_name ): string|false {
		foreach ( $this->namespace_map as $prefix => $directory ) {
			if ( str_starts_with( $class_name, $prefix ) ) {
				return $prefix;
			}
		}

		return false;
	}

	/**
	 * Normalize file path
	 *
	 * @since 1.0.0
	 * @param string $path The file path
	 * @return string The normalized path
	 */
	private function normalize_path( string $path ): string {
		// Replace multiple directory separators with single ones
		$path = preg_replace( '#[/\\]+#', DIRECTORY_SEPARATOR, $path );

		// Remove trailing directory separator
		return rtrim( $path, DIRECTORY_SEPARATOR );
	}

	/**
	 * Load a file if it exists
	 *
	 * @since 1.0.0
	 * @param string $file_path The file path
	 * @return bool True if the file was loaded, false otherwise
	 */
	private function load_file( string $file_path ): bool {
		if ( file_exists( $file_path ) && is_readable( $file_path ) ) {
			require_once $file_path;
			return true;
		}

		return false;
	}

	/**
	 * Add a namespace mapping
	 *
	 * @since 1.0.0
	 * @param string $namespace The namespace prefix
	 * @param string $directory The base directory
	 * @return void
	 */
	public function add_namespace( string $namespace, string $directory ): void {
		$namespace                         = rtrim( $namespace, '\\' ) . '\\';
		$this->namespace_map[ $namespace ] = rtrim( $directory, DIRECTORY_SEPARATOR );
	}

	/**
	 * Get all registered namespaces
	 *
	 * @since 1.0.0
	 * @return array<string, string> The namespace mappings
	 */
	public function get_namespaces(): array {
		return $this->namespace_map;
	}
}

// Initialize and register the autoloader
if ( ! isset( $GLOBALS['wppb_autoloader'] ) ) {
	$wppb_autoloader = new Autoloader();
	$wppb_autoloader->register();

	// Store globally for potential unregistration
	$GLOBALS['wppb_autoloader'] = $wppb_autoloader;
}
