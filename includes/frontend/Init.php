<?php
/**
 * Frontend Init Class.
 *
 * @package WPPB
 */

namespace WPPB\Frontend;

use WPPB\Core\Assets;

/**
 * If this file is called directly, abort.
 */
defined( 'ABSPATH' ) || die;

/**
 * The public-facing functionality of the plugin.
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

		add_action( 'wp_enqueue_scripts', array( $this->assets, 'enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this->assets, 'enqueue_styles' ) );
	}
}
