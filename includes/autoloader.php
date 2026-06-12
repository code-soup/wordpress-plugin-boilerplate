<?php
/**
 * Custom autoloader for WordPress-style filenames.
 *
 * @package WPPB
 */

namespace WPPB;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || die;

/**
 * Autoloader class.
 */
class Autoloader {

	/**
	 * Base directory.
	 *
	 * @var string
	 */
	private static $base_dir = '';

	/**
	 * Root namespace (detected from this class).
	 *
	 * @var string
	 */
	private static $root_namespace = '';

	/**
	 * Namespace to directory mappings (relative to root namespace).
	 *
	 * @var array
	 */
	private static $namespace_map = array(
		'Core\\'       => 'includes/core/',
		'Admin\\'      => 'includes/admin/',
		'Frontend\\'   => 'includes/frontend/',
		'Providers\\'  => 'includes/providers/',
		'Abstracts\\'  => 'includes/abstracts/',
		'Interfaces\\' => 'includes/interfaces/',
		'Traits\\'     => 'includes/traits/',
	);

	/**
	 * Register the autoloader.
	 *
	 * @param string $base_dir Base directory path.
	 * @return void
	 */
	public static function register( $base_dir ) {
		// Detect root namespace from this class.
		self::$root_namespace = __NAMESPACE__ . '\\';
		self::$base_dir       = rtrim( $base_dir, '/\\' ) . DIRECTORY_SEPARATOR;
		spl_autoload_register( array( __CLASS__, 'autoload' ) );
	}

	/**
	 * Autoload classes.
	 *
	 * @param string $class Full class name with namespace.
	 * @return void
	 */
	public static function autoload( $class ) {
		// Check if class belongs to our root namespace.
		if ( ! str_starts_with( $class, self::$root_namespace ) ) {
			return;
		}

		$file = self::get_file_path( $class );

		if ( $file && file_exists( $file ) ) {
			require_once $file;
		}
	}

	/**
	 * Convert class name to WordPress-style filename.
	 *
	 * @param string $class Full class name with namespace.
	 * @return string|false File path or false if not found.
	 */
	private static function get_file_path( $class ) {
		$directory = self::get_directory( $class );

		if ( ! $directory ) {
			return false;
		}

		$class_name = self::get_class_name( $class );
		$filename   = self::convert_to_filename( $class_name, $class );

		return self::$base_dir . $directory . $filename;
	}

	/**
	 * Get directory for class based on namespace.
	 *
	 * @param string $class Full class name with namespace.
	 * @return string|false Directory path or false if not found.
	 */
	private static function get_directory( $class ) {
		foreach ( self::$namespace_map as $namespace => $directory ) {
			$full_namespace = self::$root_namespace . $namespace;
			if ( str_starts_with( $class, $full_namespace ) ) {
				return $directory;
			}
		}

		return false;
	}

	/**
	 * Get class name without namespace.
	 *
	 * @param string $class Full class name with namespace.
	 * @return string Class name without namespace.
	 */
	private static function get_class_name( $class ) {
		$parts = explode( '\\', $class );
		return end( $parts );
	}

	/**
	 * Convert class name to WordPress-style filename.
	 *
	 * @param string $class_name Class name without namespace.
	 * @param string $full_class Full class name with namespace.
	 * @return string Filename.
	 */
	private static function convert_to_filename( $class_name, $full_class ) {
		$prefix = self::get_file_prefix( $class_name, $full_class );

		// Convert CamelCase to kebab-case.
		$filename = self::camel_to_kebab( $class_name );

		// Remove suffix from filename if present.
		$filename = self::remove_suffix( $filename );

		return $prefix . $filename . '.php';
	}

	/**
	 * Get file prefix based on class type.
	 *
	 * @param string $class_name Class name without namespace.
	 * @param string $full_class Full class name with namespace.
	 * @return string File prefix (class-, trait-, interface-).
	 */
	private static function get_file_prefix( $class_name, $full_class ) {
		if ( str_starts_with( $full_class, self::$root_namespace . 'Traits\\' ) ) {
			return 'trait-';
		}

		if ( str_starts_with( $full_class, self::$root_namespace . 'Interfaces\\' ) ) {
			return 'interface-';
		}

		return 'class-';
	}

	/**
	 * Convert CamelCase to kebab-case.
	 *
	 * @param string $string CamelCase string.
	 * @return string kebab-case string.
	 */
	private static function camel_to_kebab( $string ) {
		return strtolower( preg_replace( '/([a-z])([A-Z])/', '$1-$2', $string ) );
	}

	/**
	 * Remove common suffixes from filename.
	 *
	 * @param string $filename Filename.
	 * @return string Filename without suffix.
	 */
	private static function remove_suffix( $filename ) {
		$suffixes = array( '-trait', '-interface' );

		foreach ( $suffixes as $suffix ) {
			if ( substr( $filename, -strlen( $suffix ) ) === $suffix ) {
				return substr( $filename, 0, -strlen( $suffix ) );
			}
		}

		return $filename;
	}
}

