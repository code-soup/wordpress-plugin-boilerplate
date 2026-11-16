<?php
/**
 * Fired during plugin uninstall.
 *
 * @package WPPB
 */

declare( strict_types=1 );

namespace WPPB\Core;

defined( 'ABSPATH' ) || die;

/**
 * Fired during plugin deinstallation.
 *
 * This class defines all code necessary to run during the plugin's deinstallation.
 *
 * @since      2.0.2
 */
class Uninstaller {

	/**
	 * The code that runs during plugin deinstallation.
	 */
	public static function run(): void {
		// Uninstall checklist (remove comments and implement as needed):
		// - delete_option( '{prefix}_version' );
		// - delete_option( '{prefix}_settings' );
		// - delete_transient( '{prefix}_*' ); or $wpdb query for prefixed transients
		// - wp_clear_scheduled_hook( '{prefix}_daily_event' );
		// - $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}{prefix}_data" );
		// - wp_cache_flush();
	}
}
