<?php

namespace wppb\status;

// Exit if accessed directly.
defined( 'WPINC' ) || die;

/**
 * Class System_Options
 *
 * Handles the System Options page.
 *
 * @since  1.0.1
 */
class System_Options {

	/**
	 * Whether background tasks are enabled.
	 *
	 * @since  1.0.1
	 *
	 * @var null|bool
	 */
	public static $background_tasks = null;

	/**
	 * Display system options page.
	 *
	 * @since  1.0.1
	 * @access public
	 *
	 * @uses SystemOptions::get_system_options()
	 * @uses SystemOptions::prepare_item_value()
	 * @uses SystemStatus::page_footer()
	 * @uses SystemStatus::page_header()
	 */
	public static function system_options() {

		// Display page header.
		System_Status::page_header( 'Options' );

		// wp_print_styles( array( 'thickbox' ) );

		?>


		<?php

		// Display page footer.
		System_Status::page_footer();

	}



}
