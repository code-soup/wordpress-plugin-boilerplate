<?php
/**
 * Lifecycle class.
 *
 * @package WPPB
 */

namespace WPPB\Core;

use WPPB\Traits\RequirementChecksTrait;
use function WPPB\plugin;

/**
 * If this file is called directly, abort.
 */
defined( 'ABSPATH' ) || die;

/**
 * The Lifecycle class.
 */
class Lifecycle {
	use RequirementChecksTrait;

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
	 * Check plugin requirements.
	 */
	public function check_requirements(): void {
		self::run_requirement_checks( $this->plugin->config );
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
