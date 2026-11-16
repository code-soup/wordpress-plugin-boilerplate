<?php
/**
 * Trait for handling plugin requirement checks.
 *
 * @package WPPB\Traits
 */

declare(strict_types=1);

namespace WPPB\Traits;

/**
 * Trait RequirementChecksTrait
 *
 * Provides reusable methods to check for WordPress and PHP version requirements.
 */
trait RequirementChecksTrait {

	/**
	 * Run all requirement checks.
	 *
	 * @param array<string, mixed> $config The plugin configuration array.
	 * @throws \Exception If requirements not met.
	 */
	public static function run_requirement_checks( array $config ): void {
		if ( ! self::is_wp_version_ok( $config['MIN_WP_VERSION'] ) ) {
			throw new \Exception(
				sprintf(
					'Minimum WordPress version required: %s. You are running version: %s.',
					$config['MIN_WP_VERSION'],
					get_bloginfo( 'version' )
				)
			);
		}

		if ( ! self::is_php_version_ok( $config['MIN_PHP_VERSION'] ) ) {
			throw new \Exception(
				sprintf(
					'Minimum PHP version required: %s. You are running version: %s.',
					$config['MIN_PHP_VERSION'],
					phpversion()
				)
			);
		}

		$min_mysql = $config['MIN_MYSQL_VERSION'] ?? '';
		if ( is_string( $min_mysql ) && $min_mysql !== '' && ! self::is_mysql_version_ok( $min_mysql ) ) {
			throw new \Exception(
				sprintf(
					'Minimum database version required: %s. You are running version: %s.',
					$min_mysql,
					self::get_database_version()
				)
			);
		}
	}

	/**
	 * Check if the WordPress version is sufficient.
	 *
	 * @param string $min_wp_version The minimum required WordPress version.
	 * @return bool
	 */
	private static function is_wp_version_ok( string $min_wp_version ): bool {
		return version_compare( get_bloginfo( 'version' ), $min_wp_version, '>=' );
	}

	/**
	 * Check if the PHP version is sufficient.
	 *
	 * @param string $min_php_version The minimum required PHP version.
	 * @return bool
	 */
	private static function is_php_version_ok( string $min_php_version ): bool {
		return version_compare( phpversion(), $min_php_version, '>=' );
	}

	/**
	 * Check if the database server version is sufficient.
	 *
	 * @param string $min_mysql_version The minimum required database version.
	 * @return bool
	 */
	private static function is_mysql_version_ok( string $min_mysql_version ): bool {
		$current = self::get_database_version();

		if ( $current === '' ) {
			return false;
		}

		return version_compare( $current, $min_mysql_version, '>=' );
	}

	/**
	 * Get the database server version reported by WordPress.
	 *
	 * @return string
	 */
	private static function get_database_version(): string {
		global $wpdb;

		if ( ! isset( $wpdb ) || ! is_object( $wpdb ) ) {
			return '';
		}

		$version = $wpdb->db_version();

		return is_string( $version ) ? $version : '';
	}
}
