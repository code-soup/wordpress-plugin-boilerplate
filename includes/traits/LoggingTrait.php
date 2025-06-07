<?php
/**
 * Logging trait.
 *
 * @package WPPB
 */

namespace WPPB\Traits;

/**
 * If this file is called directly, abort.
 */
defined( 'ABSPATH' ) || die;

/**
 * The LoggingTrait trait.
 */
trait LoggingTrait {


	/**
	 * Log a message.
	 *
	 * @param string $message The message to log.
	 * @param string $level The log level.
	 */
	public function log( string $message, string $level = 'info' ): void {
		if ( ! defined( 'WP_DEBUG_LOG' ) || ! WP_DEBUG_LOG ) {
			return;
		}

		$message = sprintf( '[%s] %s: %s', gmdate( 'Y-m-d H:i:s' ), $level, $message );

		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		error_log( $message );
	}
}
