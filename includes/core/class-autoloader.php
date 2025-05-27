<?php

declare(strict_types=1);

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * WordPress-Compatible PSR-4 Autoloader
 *
 * This autoloader provides PSR-4 compatibility while maintaining WordPress
 * file naming conventions (e.g., class-init.php instead of Init.php).
 *
 * @since 1.0.0
 */
class WPPB_Autoloader {

	/**
	 * Namespace to directory mappings
	 *
	 * @var array<string, string>
	 * @since 1.0.0
	 */
	private array $namespace_map = array();

	/**
	 * File name transformations
	 *
	 * @var array<string, callable>
	 * @since 1.0.0
	 */
	private array $transformations = array();

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->setup_namespace_mappings();
		$this->setup_transformations();
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
	 * Load a class file
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

		// Remove the namespace prefix from the class name
		$relative_class = substr( $class_name, strlen( $namespace_prefix ) );

		// Convert namespace separators to directory separators
		$relative_path = str_replace( '\\', DIRECTORY_SEPARATOR, $relative_class );

		// Apply file name transformation
		$file_name = $this->transform_class_name_to_file_name( $relative_class );

		// Build the full file path
		$file_path = $base_directory . DIRECTORY_SEPARATOR . dirname( $relative_path ) . DIRECTORY_SEPARATOR . $file_name;

		// Normalize the path
		$file_path = $this->normalize_path( $file_path );

		// Load the file if it exists
		if ( $this->load_file( $file_path ) ) {
			return true;
		}

		// Try alternative locations for backward compatibility
		return $this->try_alternative_locations( $class_name, $relative_class );
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
	 * Setup file name transformations
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function setup_transformations(): void {
		$this->transformations = array(
			// WordPress class naming convention: class-name.php
			'wordpress' => function ( string $class_name ): string {
				// Convert PascalCase to kebab-case
				$kebab_case = strtolower( preg_replace( '/([a-z])([A-Z])/', '$1-$2', $class_name ) );
				return 'class-' . $kebab_case . '.php';
			},

			// Interface naming convention: name.php
			'interface' => function ( string $class_name ): string {
				if ( str_ends_with( $class_name, 'Interface' ) ) {
					$name = substr( $class_name, 0, -9 ); // Remove 'Interface'
					$kebab_case = strtolower( preg_replace( '/([a-z])([A-Z])/', '$1-$2', $name ) );
					return $kebab_case . '.php';
				}
				return $this->transformations['wordpress']( $class_name );
			},

			// Abstract class naming convention: abstract-name.php
			'abstract' => function ( string $class_name ): string {
				if ( str_starts_with( $class_name, 'Abstract' ) ) {
					$name = substr( $class_name, 8 ); // Remove 'Abstract'
					$kebab_case = strtolower( preg_replace( '/([a-z])([A-Z])/', '$1-$2', $name ) );
					return 'abstract-' . $kebab_case . '.php';
				}
				return $this->transformations['wordpress']( $class_name );
			},

			// Trait naming convention: trait-name.php or name.php
			'trait' => function ( string $class_name ): string {
				if ( str_ends_with( $class_name, 'Trait' ) ) {
					$name = substr( $class_name, 0, -5 ); // Remove 'Trait'
					$kebab_case = strtolower( preg_replace( '/([a-z])([A-Z])/', '$1-$2', $name ) );
					return $kebab_case . '.php';
				}
				return $this->transformations['wordpress']( $class_name );
			},

			// Service Provider naming convention: name-service-provider.php
			'provider' => function ( string $class_name ): string {
				if ( str_ends_with( $class_name, 'ServiceProvider' ) ) {
					$name = substr( $class_name, 0, -15 ); // Remove 'ServiceProvider'
					$kebab_case = strtolower( preg_replace( '/([a-z])([A-Z])/', '$1-$2', $name ) );
					return $kebab_case . '-service-provider.php';
				}
				return $this->transformations['wordpress']( $class_name );
			},
		);
	}

	/**
	 * Transform class name to file name
	 *
	 * @since 1.0.0
	 * @param string $class_name The class name (without namespace)
	 * @return string The transformed file name
	 */
	private function transform_class_name_to_file_name( string $class_name ): string {
		// Remove leading backslash and get just the class name
		$class_name = basename( str_replace( '\\', '/', $class_name ) );

		// Determine the appropriate transformation (order matters!)
		if ( str_ends_with( $class_name, 'ServiceProvider' ) ) {
			return $this->transformations['provider']( $class_name );
		}

		if ( str_ends_with( $class_name, 'Interface' ) ) {
			return $this->transformations['interface']( $class_name );
		}

		if ( str_starts_with( $class_name, 'Abstract' ) ) {
			return $this->transformations['abstract']( $class_name );
		}

		if ( str_ends_with( $class_name, 'Trait' ) ) {
			return $this->transformations['trait']( $class_name );
		}

		// Default to WordPress convention
		return $this->transformations['wordpress']( $class_name );
	}

	/**
	 * Find the namespace prefix for a class
	 *
	 * @since 1.0.0
	 * @param string $class_name The fully qualified class name
	 * @return string|false The namespace prefix or false if not found
	 */
	private function find_namespace_prefix( string $class_name ) {
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
		$path = preg_replace( '#[/\\\\]+#', DIRECTORY_SEPARATOR, $path );

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
	 * Try alternative file locations for backward compatibility
	 *
	 * @since 1.0.0
	 * @param string $full_class_name The fully qualified class name
	 * @param string $relative_class The class name without namespace prefix
	 * @return bool True if a file was loaded, false otherwise
	 */
	private function try_alternative_locations( string $full_class_name, string $relative_class ): bool {
		$plugin_dir = dirname( dirname( __DIR__ ) );

		// Alternative file name patterns to try
		$class_name = basename( str_replace( '\\', '/', $relative_class ) );
		$alternatives = array(
			// Standard PSR-4 naming
			$class_name . '.php',
			// Lowercase with hyphens
			strtolower( str_replace( '_', '-', $class_name ) ) . '.php',
			// Lowercase with underscores
			strtolower( $class_name ) . '.php',
		);

		// Directories to search in
		$search_dirs = array(
			$plugin_dir . '/includes',
			$plugin_dir . '/includes/core',
			$plugin_dir . '/includes/admin',
			$plugin_dir . '/includes/frontend',
		);

		foreach ( $search_dirs as $dir ) {
			foreach ( $alternatives as $alt_filename ) {
				$alt_path = $dir . DIRECTORY_SEPARATOR . $alt_filename;
				if ( $this->load_file( $alt_path ) ) {
					return true;
				}
			}
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
		$namespace = rtrim( $namespace, '\\' ) . '\\';
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
	$wppb_autoloader = new WPPB_Autoloader();
	$wppb_autoloader->register();

	// Store globally for potential unregistration
	$GLOBALS['wppb_autoloader'] = $wppb_autoloader;
} 