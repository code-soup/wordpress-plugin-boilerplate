<?php
/**
 * Admin Init class.
 *
 * @package WPPB
 */

declare(strict_types=1);

namespace WPPB\Admin;

use WPPB\Core\Assets;

/**
 * If this file is called directly, abort.
 */
defined( 'ABSPATH' ) || die;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 */
class Init {

	/**
	 * The assets instance.
	 *
	 * @var Assets
	 */
	protected Assets $assets;

	/**
	 * Init constructor.
	 */
	public function __construct() {
		$this->assets = new Assets();

		add_action( 'admin_enqueue_scripts', array( $this->assets, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this->assets, 'enqueue_styles' ) );
	}
}
