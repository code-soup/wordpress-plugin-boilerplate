<?php

namespace wppb\status;

// Exit if accessed directly.
defined( 'WPINC' ) || die;


/**
 * Class Helpers
 *
 * Handles the helper functions for the System Status page.
 *
 * @since  1.0.1
 */
class Helpers {

	/**
	 * Determines if a value is empty.
	 *
	 * @since  Unknown
	 * @access public
	 *
	 * @param string $name  The property name to check.
	 * @param array  $array Optional. An array to check through.  Otherwise, checks for POST variables.
	 *
	 * @return bool True if empty.  False otherwise.
	 */
	public static function rgempty( $name, $array = null ) {

		if ( is_array( $name ) ) {
			return empty( $name );
		}

		if ( ! $array ) {
			$array = $_POST;
		}

		$val = Helpers::rgar( $array, $name );

		return empty( $val );
	}



	/**
	 * Get a specific property of an array without needing to check if that property exists.
	 *
	 * Provide a default value if you want to return a specific value if the property is not set.
	 *
	 * @since  Unknown
	 * @access public
	 *
	 * @param array  $array   Array from which the property's value should be retrieved.
	 * @param string $prop    Name of the property to be retrieved.
	 * @param string $default Optional. Value that should be returned if the property is not set or empty. Defaults to null.
	 *
	 * @return null|string|mixed The value
	 */
	public static function rgar( $array, $prop, $default = null ) {

		if ( ! is_array( $array ) && ! ( is_object( $array ) && $array instanceof ArrayAccess ) ) {
			return $default;
		}

		if ( isset( $array[ $prop ] ) ) {
			$value = $array[ $prop ];
		} else {
			$value = '';
		}

		return empty( $value ) && $default !== null ? $default : $value;
	}


	/**
	 * Gets a specific property within a multidimensional array.
	 *
	 * @since  Unknown
	 * @access public
	 *
	 * @param array  $array   The array to search in.
	 * @param string $name    The name of the property to find.
	 * @param string $default Optional. Value that should be returned if the property is not set or empty. Defaults to null.
	 *
	 * @return null|string|mixed The value
	 */
	public static function rgars( $array, $name, $default = null ) {

		if ( ! is_array( $array ) && ! ( is_object( $array ) && $array instanceof ArrayAccess ) ) {
			return $default;
		}

		$names = explode( '/', $name );
		$val   = $array;
		foreach ( $names as $current_name ) {
			$val = Helpers::rgar( $val, $current_name, $default );
		}

		return $val;
	}

}
