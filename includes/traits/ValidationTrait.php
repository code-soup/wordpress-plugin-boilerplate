<?php

declare(strict_types=1);

namespace WPPB\Traits;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Validation methods for input sanitization and validation
 *
 * @since 1.0.0
 */
trait ValidationTrait {
	/**
	 * Sanitize and validate an email address
	 *
	 * @since 1.0.0
	 * @param string $email Email address to validate
	 * @return string|false Sanitized email or false if invalid
	 */
	private function validate_email( string $email ) {
		$email = sanitize_email( $email );
		return is_email( $email ) ? $email : false;
	}

	/**
	 * Sanitize and validate a URL
	 *
	 * @since 1.0.0
	 * @param string $url URL to validate
	 * @return string Sanitized URL
	 */
	private function validate_url( string $url ): string {
		return esc_url_raw( $url );
	}

	/**
	 * Sanitize text input
	 *
	 * @since 1.0.0
	 * @param string $text Text to sanitize
	 * @return string Sanitized text
	 */
	private function sanitize_text( string $text ): string {
		return sanitize_text_field( $text );
	}

	/**
	 * Sanitize textarea input
	 *
	 * @since 1.0.0
	 * @param string $text Text to sanitize
	 * @return string Sanitized text
	 */
	private function sanitize_textarea( string $text ): string {
		return sanitize_textarea_field( $text );
	}

	/**
	 * Validate and sanitize an integer
	 *
	 * @since 1.0.0
	 * @param mixed $number Number to validate
	 * @return int Sanitized integer
	 */
	private function validate_int( $number ): int {
		return intval( $number );
	}

	/**
	 * Validate and sanitize a float
	 *
	 * @since 1.0.0
	 * @param mixed $number Number to validate
	 * @return float Sanitized float
	 */
	private function validate_float( $number ): float {
		return floatval( $number );
	}

	/**
	 * Validate a boolean value
	 *
	 * @since 1.0.0
	 * @param mixed $value Value to validate
	 * @return bool Validated boolean
	 */
	private function validate_bool( $value ): bool {
		return (bool) $value;
	}
}
