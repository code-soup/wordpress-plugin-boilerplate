<?php

namespace wppb;

// Exit if accessed directly
defined( 'WPINC' ) || die;

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 */
class Activator {

	public static function activate() {

        // Test is it working
        update_option( '__activate', time() );

        error_log( 'Activator' );
        error_log( function_exists('update_option') );

        return 'activate';
	}
}
