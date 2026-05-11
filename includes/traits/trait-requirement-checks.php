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
}
