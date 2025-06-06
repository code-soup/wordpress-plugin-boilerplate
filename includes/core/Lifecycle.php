<?php
/**
 * Lifecycle class.
 *
 * @package WPPB
 */

namespace WPPB\Core;

/**
 * If this file is called directly, abort.
 */
defined( 'ABSPATH' ) || die;

/**
 * The Lifecycle class.
 */
class Lifecycle {

	/**
	 * The minimum required WordPress version.
	 *
	 * @var string
	 */
	protected string $min_wp_version = '5.0';

	/**
	 * The minimum required PHP version.
	 *
	 * @var string
	 */
	protected string $min_php_version = '7.4';

	/**
	 * The plugin name.
	 *
	 * @var string
	 */
	protected string $plugin_name = 'WordPress Plugin Boilerplate';

	/**
	 * Check if the minimum requirements are met.
	 */
	public function check_requirements(): void {
		if ( ! $this->is_wp_version_ok() ) {
			$this->deactivate_plugin(
				sprintf(
					// translators: 1. Minimum WordPress version, 2. Current WordPress version.
					esc_html__( 'Minimum WordPress version required: %1$s. You are running version: %2$s.', 'WPPB' ),
					$this->min_wp_version,
					get_bloginfo( 'version' )
				)
			);
		}

		if ( ! $this->is_php_version_ok() ) {
			$this->deactivate_plugin(
				sprintf(
					// translators: 1. Minimum PHP version, 2. Current PHP version.
					esc_html__( 'Minimum PHP version required: %1$s. You are running version: %2$s.', 'WPPB' ),
					$this->min_php_version,
					phpversion()
				)
			);
		}
	}

	/**
	 * Check if the WordPress version is ok.
	 *
	 * @return bool
	 */
	protected function is_wp_version_ok(): bool {
		return version_compare( get_bloginfo( 'version' ), $this->min_wp_version, '>=' );
	}

	/**
	 * Check if the PHP version is ok.
	 *
	 * @return bool
	 */
	protected function is_php_version_ok(): bool {
		return version_compare( phpversion(), $this->min_php_version, '>=' );
	}

	/**
	 * Deactivate the plugin.
	 *
	 * @param string $message The message to display.
	 */
	protected function deactivate_plugin( string $message ): void {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die( esc_html( $message ) );
	}

	/**
	 * The code that runs during plugin activation.
	 */
	public static function activate(): void {
		// Do something on activation.
		flush_rewrite_rules();
	}

	/**
	 * The code that runs during plugin deactivation.
	 */
	public static function deactivate(): void {
		// Do something on deactivation.
		flush_rewrite_rules();
	}

	/**
	 * The code that runs during plugin uninstallation.
	 */
	public static function uninstall(): void {
		// Do something on uninstall.
		self::cleanup_options();
		self::cleanup_tables();
	}

	/**
	 * Cleanup options.
	 */
	protected static function cleanup_options(): void {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$options = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT option_name FROM $wpdb->options WHERE option_name LIKE %s",
				$wpdb->esc_like( 'wppb_' ) . '%'
			)
		);

		foreach ( $options as $option ) {
			delete_option( $option );
		}
	}

	/**
	 * Cleanup tables.
	 */
	protected static function cleanup_tables(): void {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$tables = $wpdb->get_col(
			$wpdb->prepare(
				'SELECT table_name FROM information_schema.tables WHERE table_name LIKE %s',
				$wpdb->esc_like( 'wppb_' ) . '%'
			)
		);

		foreach ( $tables as $table ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
			$wpdb->query( 'DROP TABLE IF EXISTS ' . esc_sql( $table ) );
		}
	}
}
