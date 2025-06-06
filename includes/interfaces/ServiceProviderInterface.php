<?php
/**
 * Service Provider Interface.
 *
 * @package WPPB
 */

namespace WPPB\Interfaces;

use WPPB\Core\Container;

/**
 * If this file is called directly, abort.
 */
defined( 'ABSPATH' ) || die;

/**
 * The ServiceProviderInterface interface.
 */
interface ServiceProviderInterface {

	/**
	 * Register the service provider.
	 */
	public function register(): void;

	/**
	 * Get the container.
	 *
	 * @return Container
	 */
	public function get_container(): Container;
}
