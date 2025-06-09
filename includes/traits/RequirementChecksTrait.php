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
	 */
	public static function run_requirement_checks( array $config ): void {
		if ( ! self::is_wp_version_ok( $config['MIN_WP_VERSION'] ) ) {
			self::deactivate_plugin(
				$config['PLUGIN_BASENAME'],
				sprintf(
					// translators: 1. Minimum WordPress version, 2. Current WordPress version.
					esc_html__( 'Minimum WordPress version required: %1$s. You are running version: %2$s.', '__PLUGIN_TEXTDOMAIN__' ),
					$config['MIN_WP_VERSION'],
					get_bloginfo( 'version' )
				)
			);
		}

		if ( ! self::is_php_version_ok( $config['MIN_PHP_VERSION'] ) ) {
			self::deactivate_plugin(
				$config['PLUGIN_BASENAME'],
				sprintf(
					// translators: 1. Minimum PHP version, 2. Current PHP version.
					esc_html__( 'Minimum PHP version required: %1$s. You are running version: %2$s.', '__PLUGIN_TEXTDOMAIN__' ),
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

	/**
	 * Deactivate the plugin and show an error message.
	 *
	 * @param string $plugin_basename The plugin's basename.
	 * @param string $message The message to display.
	 */
	private static function deactivate_plugin( string $plugin_basename, string $message ): void {
		deactivate_plugins( $plugin_basename );
		wp_die( esc_html( $message ) );
	}
}
