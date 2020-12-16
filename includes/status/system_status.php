<?php

namespace wppb\status;

// Exit if accessed directly.
defined( 'WPINC' ) || die;


/**
 * Class System_Status
 *
 * Handles the System Status page.
 *
 * @since  1.0.1
 */
class System_Status {


	/**
	 * Determines which system status page to display.
	 *
	 * @since  1.0.1
	 * @access public
	 */
	public static function system_status_page() {

		$subview = 'report';
		System_Report::system_report();
		do_action( "cs_wppb_system_status_page_{$subview}" );

	}

	/**
	 * Get System Status page subviews.
	 *
	 * @since  1.0.1
	 * @access public
	 *
	 * @return array
	 */
	public static function get_subviews() {
		// Define default subview.
		$subviews = array(
			10 => array(
				'name'  => 'report',
				'label' => __( 'System Report', 'cs-wppb' ),
			),
		);

		/**
		 * Modify menu items which will appear in the System Status menu.
		 *
		 * @since  1.0.1
		 * @param array $subviews An array of menu items to be displayed on the System Status page.
		 */
		$subviews = apply_filters( 'cs_wppb_system_status_menu', $subviews );

		ksort( $subviews, SORT_NUMERIC );

		return $subviews;
	}


	/**
	 * Render System Status page header.
	 *
	 * @since  1.0.1
	 * @access public
	 *
	 * @param string $title Page title.
	 *
	 * @uses Common::display_dismissible_message()
	 * @uses Common::get_browser_class()
	 */
	public static function page_header( $title = '' ) {
		// Print admin styles.
		// wp_print_styles( array( 'jquery-ui-styles', 'cs_wppb_admin', 'wp-pointer' ) );

		?>

		<div class="wrap <?php echo Common::get_browser_class(); ?>">

			<h2><?php esc_html_e( $title, 'cs-wppb' ); ?></h2>

			<?php
			Common::display_admin_message();
			Common::display_dismissible_message();
			?>



		<?php
	}

		/**
		 * Render System Status page footer.
		 *
		 * @since  1.0.1
		 * @access public
		 */
	public static function page_footer() {
		?>

		</div> <!-- / wrap -->

		<?php
	}
}
