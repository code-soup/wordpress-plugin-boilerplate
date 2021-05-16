<?php

namespace wppb;

// Exit if accessed directly
defined( 'WPINC' ) || die;


/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 */
class Deactivator {

	public static function deactivate() {
        // Put code that you want to run on deactivation in here
        update_option( '_deactivator', time() );
	}

}
