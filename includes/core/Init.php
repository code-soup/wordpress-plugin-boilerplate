<?php
/**
 * Core Init Class.
 *
 * @package WPPB
 */

namespace WPPB\Core;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 */
final class Init {

	/**
	 * The Hooker instance.
	 *
	 * @var Hooker
	 */
	private Hooker $hooker;

	/**
	 * Plugin lifecycle manager
	 *
	 * @since 1.0.0
	 * @access private
	 * @var Lifecycle
	 */
	private Lifecycle $lifecycle;

	/**
	 * Class constructor.
	 *
	 * @param Hooker    $hooker    The dependency injection container.
	 * @param Lifecycle $lifecycle The plugin lifecycle manager.
	 */
	public function __construct( Hooker $hooker, Lifecycle $lifecycle ) {
		$this->hooker    = $hooker;
		$this->lifecycle = $lifecycle;
	}

	/**
	 * Initialize the plugin.
	 *
	 * @throws \Exception If the plugin is not compatible.
	 */
	public function init(): void {
		if ( ! $this->is_compatible() ) {
			return;
		}

		$this->hooker->run();
	}

	/**
	 * Check if the plugin is compatible with the current environment.
	 *
	 * @return bool
	 * @throws \Exception If a compatibility check fails.
	 */
	private function is_compatible(): bool {
		try {
			$this->lifecycle->check_requirements();

			return true;
		} catch ( \Exception $e ) {
			add_action(
				'admin_notices',
				function () use ( $e ) {
					echo '<div class="notice notice-error"><p>' . esc_html( $e->getMessage() ) . '</p></div>';
				}
			);

			return false;
		}
	}
}
