<?php
/**
 * Lifecycle class.
 *
 * @package WPPB
 */

namespace WPPB\Core;

use function WPPB\plugin;

/**
 * If this file is called directly, abort.
 */
defined( 'ABSPATH' ) || die;

/**
 * The Lifecycle class.
 */
class Lifecycle {

	/**
	 * The plugin instance.
	 *
	 * @var Plugin
	 */
	protected Plugin $plugin;

	/**
	 * Constructor.
	 *
	 * @param Plugin $plugin The main plugin instance.
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Check if the minimum requirements are met.
	 */
	public function check_requirements(): void {
		if ( ! $this->is_wp_version_ok() ) {
			$this->deactivate_plugin(
				sprintf(
					// translators: 1. Minimum WordPress version, 2. Current WordPress version.
					esc_html__( 'Minimum WordPress version required: %1$s. You are running version: %2$s.', '__PLUGIN_TEXTDOMAIN__' ),
					$this->plugin->config['MIN_WP_VERSION'],
					get_bloginfo( 'version' )
				)
			);
		}

		if ( ! $this->is_php_version_ok() ) {
			$this->deactivate_plugin(
				sprintf(
					// translators: 1. Minimum PHP version, 2. Current PHP version.
					esc_html__( 'Minimum PHP version required: %1$s. You are running version: %2$s.', '__PLUGIN_TEXTDOMAIN__' ),
					$this->plugin->config['MIN_PHP_VERSION'],
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
		return version_compare( get_bloginfo( 'version' ), $this->plugin->config['MIN_WP_VERSION'], '>=' );
	}

	/**
	 * Check if the PHP version is ok.
	 *
	 * @return bool
	 */
	protected function is_php_version_ok(): bool {
		return version_compare( phpversion(), $this->plugin->config['MIN_PHP_VERSION'], '>=' );
	}

	/**
	 * Deactivate the plugin.
	 *
	 * @param string $message The message to display.
	 */
	protected function deactivate_plugin( string $message ): void {
		deactivate_plugins( $this->plugin->config['PLUGIN_BASENAME'] );
		wp_die( esc_html( $message ) );
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
				$wpdb->esc_like( plugin()->config['PLUGIN_PREFIX'] ) . '%'
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
				$wpdb->esc_like( plugin()->config['PLUGIN_PREFIX'] ) . '%'
			)
		);

		foreach ( $tables as $table ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
			$wpdb->query( 'DROP TABLE IF EXISTS ' . esc_sql( $table ) );
		}
	}
}
