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
	 * The validator instance.
	 *
	 * @var v
	 */
	protected v $validator;

	/**
	 * ValidationTrait constructor.
	 */
	public function __construct() {
		$this->validator = new v();
	}

	/**
	 * Get the validator instance.
	 *
	 * @return v
	 */
	public function get_validator(): v {
		return $this->validator;
	}

	/**
	 * Set the validator instance.
	 *
	 * @param v $validator The validator instance.
	 */
	public function set_validator( v $validator ): void {
		$this->validator = $validator;
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
		return $this->validator->is( $rule, $value );
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
		return $this->validate( $value, 'notEmpty' );
	}

	/**
	 * Validate a value is a string.
	 *
	 * @param mixed $value The value to validate.
	 *
	 * @return bool
	 */
	public function is_string( $value ): bool {
		return $this->validate( $value, 'stringType' );
	}

	/**
	 * Validate a value is an integer.
	 *
	 * @param mixed $value The value to validate.
	 *
	 * @return bool
	 */
	public function is_int( $value ): bool {
		return $this->validate( $value, 'intType' );
	}

	/**
	 * Validate a value is a float.
	 *
	 * @param mixed $value The value to validate.
	 *
	 * @return bool
	 */
	public function is_float( $value ): bool {
		return $this->validate( $value, 'floatType' );
	}

	/**
	 * Validate a value is a boolean.
	 *
	 * @param mixed $value The value to validate.
	 *
	 * @return bool
	 */
	public function is_bool( $value ): bool {
		return $this->validate( $value, 'boolType' );
	}

	/**
	 * Validate a value is an array.
	 *
	 * @param mixed $value The value to validate.
	 *
	 * @return bool
	 */
	public function is_array( $value ): bool {
		return $this->validate( $value, 'arrayType' );
	}

	/**
	 * Validate a value is an object.
	 *
	 * @param mixed $value The value to validate.
	 *
	 * @return bool
	 */
	public function is_object( $value ): bool {
		return $this->validate( $value, 'objectType' );
	}
}
