<?php

declare(strict_types=1);

namespace WPPB\Traits;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Logging methods for error handling and debugging
 *
 * @since 1.0.0
 */
trait LoggingTrait {
	/**
	 * Log a message to the WordPress debug log
	 *
	 * @since 1.0.0
	 * @param mixed $message Message to log
	 * @param string $level Log level (debug, info, warning, error)
	 * @return void
	 */
	private function log_message( $message, string $level = 'debug' ): void {
		if ( ! WP_DEBUG ) {
			return;
		}

		// Get plugin name if method exists, otherwise use default
		$plugin_name = method_exists( $this, 'get_plugin_name' )
			? $this->get_plugin_name()
			: 'WPPB';

		$formatted_message = sprintf(
			'[%s] [%s] %s',
			$plugin_name,
			strtoupper( $level ),
			is_string( $message ) ? $message : print_r( $message, true )
		);

		error_log( $formatted_message );
	}

	/**
	 * Log a debug message
	 *
	 * @since 1.0.0
	 * @param mixed $message Message to log
	 * @return void
	 */
	private function debug( $message ): void {
		$this->log_message( $message, 'debug' );
	}

	/**
	 * Log an info message
	 *
	 * @since 1.0.0
	 * @param mixed $message Message to log
	 * @return void
	 */
	private function info( $message ): void {
		$this->log_message( $message, 'info' );
	}

	/**
	 * Log a warning message
	 *
	 * @since 1.0.0
	 * @param mixed $message Message to log
	 * @return void
	 */
	private function warning( $message ): void {
		$this->log_message( $message, 'warning' );
	}

	/**
	 * Log an error message
	 *
	 * @since 1.0.0
	 * @param mixed $message Message to log
	 * @return void
	 */
	private function error( $message ): void {
		$this->log_message( $message, 'error' );
	}

	/**
	 * Log an exception
	 *
	 * @since 1.0.0
	 * @param \Throwable $exception Exception to log
	 * @return void
	 */
	private function log_exception( \Throwable $exception ): void {
		$message = sprintf(
			'Exception: %s in %s on line %d. Trace: %s',
			$exception->getMessage(),
			$exception->getFile(),
			$exception->getLine(),
			$exception->getTraceAsString()
		);

		$this->error( $message );
	}
}
