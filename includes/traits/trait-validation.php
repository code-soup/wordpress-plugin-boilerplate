<?php
/**
 * Validation trait.
 *
 * @package WPPB
 */

namespace WPPB\Traits;

use Respect\Validation\Validator as v;

/**
 * If this file is called directly, abort.
 */
defined( 'ABSPATH' ) || die;

/**
 * The ValidationTrait trait.
 */
trait ValidationTrait {


	/**
	 * Get the validator instance (lazy initialization).
	 *
	 * @return v
	 */
	protected function get_validator(): v {
		return v::class;
	}

	/**
	 * Validate a value against a rule.
	 *
	 * @param mixed  $value The value to validate.
	 * @param string $rule The rule to validate against.
	 *
	 * @return bool
	 */
	public function validate( $value, string $rule ): bool {
		try {
			return v::$rule()->validate( $value );
		} catch ( \Exception $e ) {
			return false;
		}
	}

	/**
	 * Validate an array of values against an array of rules.
	 *
	 * @param array $values The values to validate.
	 * @param array $rules The rules to validate against.
	 *
	 * @return bool
	 */
	public function validate_array( array $values, array $rules ): bool {
		foreach ( $rules as $field => $rule ) {
			if ( ! isset( $values[ $field ] ) || ! $this->validate( $values[ $field ], $rule ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Validate a value is not empty.
	 *
	 * @param mixed $value The value to validate.
	 *
	 * @return bool
	 */
	public function is_not_empty( $value ): bool {
		return v::notEmpty()->validate( $value );
	}

	/**
	 * Validate a value is a string.
	 *
	 * @param mixed $value The value to validate.
	 *
	 * @return bool
	 */
	public function is_string( $value ): bool {
		return v::stringType()->validate( $value );
	}

	/**
	 * Validate a value is an integer.
	 *
	 * @param mixed $value The value to validate.
	 *
	 * @return bool
	 */
	public function is_int( $value ): bool {
		return v::intType()->validate( $value );
	}

	/**
	 * Validate a value is a float.
	 *
	 * @param mixed $value The value to validate.
	 *
	 * @return bool
	 */
	public function is_float( $value ): bool {
		return v::floatType()->validate( $value );
	}

	/**
	 * Validate a value is a boolean.
	 *
	 * @param mixed $value The value to validate.
	 *
	 * @return bool
	 */
	public function is_bool( $value ): bool {
		return v::boolType()->validate( $value );
	}

	/**
	 * Validate a value is an array.
	 *
	 * @param mixed $value The value to validate.
	 *
	 * @return bool
	 */
	public function is_array( $value ): bool {
		return v::arrayType()->validate( $value );
	}

	/**
	 * Validate a value is an object.
	 *
	 * @param mixed $value The value to validate.
	 *
	 * @return bool
	 */
	public function is_object( $value ): bool {
		return v::objectType()->validate( $value );
	}
}
