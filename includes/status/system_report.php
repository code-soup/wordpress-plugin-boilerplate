<?php

namespace wppb\status;

use wppb\status\Common;
use wppb\status\Helpers;

// Exit if accessed directly.
defined( 'WPINC' ) || die;

/**
 * Class System_Report
 *
 * Handles the System Report view on the System Status page.
 *
 * @since  1.0.1
 */
class System_Report {

	/**
	 * Whether background tasks are enabled.
	 *
	 * @since  1.0.1
	 *
	 * @var null|bool
	 */
	public static $background_tasks = null;

	/**
	 * Display system report page.
	 *
	 * @since  1.0.1
	 * @access public
	 *
	 * @uses SystemReport::get_system_report()
	 * @uses SystemReport::prepare_item_value()
	 * @uses SystemStatus::page_footer()
	 * @uses SystemStatus::page_header()
	 */
	public static function system_report() {

		// Display page header.
		System_Status::page_header( 'System Status' );

		// Get system report sections.
		$sections           = self::get_system_report();
		$system_report_text = self::get_system_report_text( $sections );

		// wp_print_styles( array( 'thickbox' ) );

		?>
		<div class="wrap">
			<div class="updated cs_wppb_system_report_alert inline">
				<p><?php _e( 'The following is a system report containing useful technical information for troubleshooting issues. If you need further help after viewing the report, click on the "Copy System Report" button below to copy the report and paste it in your message to support.', 'cs-wppb' ); ?></p>
				<p class="inline"><a href="#" class="button-primary" id="cs_wppb_copy_report" data-clipboard-target="#System_Report"><?php _e( 'Copy System Report', 'cs-wppb' ); ?></a></p>

				<div class="cs_wppb_copy_message inline" id="cs_wppb_copy_error_message">
					<p><span class="dashicons dashicons-yes"></span>
					<?php
					esc_html_e( 'Report generated!', 'cs-wppb' );
					echo ' <b>Press Ctrl+C to copy it.</b>';
					?>
					</p>
				</div>

				<div class="cs_wppb_copy_message inline" id="cs_wppb_copy_success">
					<p><span class="dashicons dashicons-yes"></span><?php esc_html_e( 'Report Copied!', 'cs-wppb' ); ?></p>
				</div>

				<textarea id="System_Report" readonly="readonly" ><?php echo esc_html( $system_report_text ); ?></textarea>
			</div>
		</div>

		<form method="post" id="System_Report_form">
			<input type="hidden" name="cs_wppb_action" id="cs_wppb_action" />
			<input type="hidden" name="cs_wppb_arg" id="cs_wppb_arg" />

		<?php
		wp_nonce_field( 'cs_wppb_sytem_report_action', 'cs_wppb_sytem_report_action' );

		// Loop through system report sections.
		foreach ( $sections as $i => $section ) {

			// Display section title.
			echo '<h3><span>' . $section['title'] . '</span></h3>';

			// Loop through tables.
			foreach ( $section['tables'] as $table ) {

				if ( ! isset( $table['items'] ) || empty( $table['items'] ) ) {
					continue;
				}

				// Open section table.
				echo '<table class="cs_wppb_system_report wp-list-table widefat fixed striped feeds">';

				// Add table header.
				echo '<thead><tr><th colspan="2">' . Helpers::rgar( $table, 'title' ) . '</th></tr></thead>';

				// Open table body.
				echo '<tbody id="the-list" data-wp-lists="list:feed">';

				// Loop through section items.
				foreach ( $table['items'] as $item ) {

					if ( Helpers::rgar( $item, 'export_only' ) ) {
						continue;
					}

					// Open item row.
					echo '<tr>';

					// Display item label.
					echo '<td data-export-label="' . esc_attr( $item['label'] ) . '">' . $item['label'] . '</td>';

					// Display item value.
					echo '<td>' . self::prepare_item_value( $item ) . '</td>';

					// Close item row.
					echo '</tr>';

				}

				// Close section table.
				echo '</tbody></table><br />';

			}

			// Add horizontal divider.
			echo $i !== count( $sections ) - 1 ? '<div class="hr-divider"></div>' : '';

		}

		// Close form.
		echo '</form>';

		// Display page footer.
		System_Status::page_footer();

	}

	/**
	 * Generate copyable system report.
	 *
	 * @since  1.0.1
	 * @access public
	 *
	 * @param array $sections System report sections.
	 *
	 * @return string
	 */
	public static function get_system_report_text( $sections ) {

		// Initialize system report text.
		$system_report_text = '';

		// Loop through system report sections.
		foreach ( $sections as $section ) {

			// Loop through tables.
			foreach ( $section['tables'] as $table ) {

				// If table has no items, skip it.
				if ( ! isset( $table['items'] ) || empty( $table['items'] ) ) {
					continue;
				}

				// Add table title to system report.
				$system_report_text .= "\n### " . self::get_export( $table, 'title' ) . " ###\n\n";

				// Loop through section items.
				foreach ( $table['items'] as $item ) {

					// Add section item to system report.
					$system_report_text .= self::get_export( $item, 'label' ) . ': ' . self::prepare_item_value( $item, true ) . "\n";

				}
			}
		}

		$system_report_text = str_replace( array( '()', '../' ), array( '', '[DT]' ), $system_report_text );

		return $system_report_text;

	}

	/**
	 * Get item value for system report.
	 *
	 * @since  1.0.1
	 * @access public
	 *
	 * @param array  $array Array of items.
	 * @param string $item  Item to get value of.
	 *
	 * @return string
	 */
	public static function get_export( $array, $item ) {

		// Get value.
		$value = isset( $array[ "{$item}_export" ] ) ? $array[ "{$item}_export" ] : $array[ $item ];

		return is_string( $value ) ? trim( $value ) : $value;

	}

	/**
	 * Prepare system report for System Status page.
	 *
	 * @since  1.0.1
	 * @access public
	 *
	 * @uses SystemReport::get_active_plugins()
	 * @uses SystemReport::get_available_logs()
	 * @uses SystemReport::get_gravityforms()
	 * @uses SystemReport::get_database()
	 * @uses SystemReport::get_network_active_plugins()
	 * @uses wpdb::db_version()
	 * @uses wpdb::get_var()
	 *
	 * @return array
	 */
	public static function get_system_report() {

		global $wpdb, $wp_version;

		$wp_cron_disabled  = defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON;
		$alternate_wp_cron = defined( 'ALTERNATE_WP_CRON' ) && ALTERNATE_WP_CRON;

		$args = array(
			'timeout'   => 2,
			'body'      => 'test',
			'cookies'   => $_COOKIE,
			'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
		);

		$query_args = array(
			'action' => 'cs_wppb_check_background_tasks',
			'nonce'  => wp_create_nonce( 'cs_wppb_check_background_tasks' ),
		);

		$url = add_query_arg( $query_args, admin_url( 'admin-ajax.php' ) );

		$response = wp_remote_post( $url, $args );

		$background_tasks = wp_remote_retrieve_body( $response ) == 'ok';

		$background_validation_message = '';
		if ( is_wp_error( $response ) ) {
			$background_validation_message = $response->get_error_message();
		} elseif ( ! $background_tasks ) {
			$response_code = wp_remote_retrieve_response_code( $response );
			if ( $response_code == 200 ) {
				$background_validation_message = esc_html__( 'Unexpected content in the response.', 'cs-wppb' );
			} else {
				$background_validation_message = sprintf( esc_html__( 'Response code: %s', 'cs-wppb' ), $response_code );
			}
		}
		self::$background_tasks = $background_tasks;

		// Prepare system report.
		$system_report = array(
			/**
			 * Use this template for the plugin info
			 */
			// array(
			// 'title'        => esc_html__( 'WordPress Plugin Boilerplate Environment', 'cs-wppb' ),
			// 'title_export' => 'WordPress Plugin Boilerplate Environment',
			// 'tables'       => array(
			// array(
			// 'title'        => esc_html__( 'WordPress Plugin Boilerplate', 'cs-wppb' ),
			// 'title_export' => 'WordPress Plugin Boilerplate',
			// 'items'        => self::get_gravityforms(),
			// ),
			// array(
			// 'title'        => esc_html__( 'Add-Ons', 'cs-wppb' ),
			// 'title_export' => 'Add-Ons',
			// 'items'        => self::get_active_plugins( false, true, false ),
			// ),
			// array(
			// 'title'        => esc_html__( 'Database', 'cs-wppb' ),
			// 'title_export' => 'Database',
			// 'items'        => self::get_database(),
			// ),
			// array(
			// 'title'        => esc_html__( 'Log Files', 'cs-wppb' ),
			// 'title_export' => 'Log Files',
			// 'items'        => self::get_available_logs(),
			// ),
			// ),
			// ),
			array(
				'title'        => esc_html__( 'WordPress Environment', 'cs-wppb' ),
				'title_export' => 'WordPress Environment',
				'tables'       => array(
					array(
						'title'        => esc_html__( 'WordPress', 'cs-wppb' ),
						'title_export' => 'WordPress',
						'items'        => array(
							array(
								'label'        => esc_html__( 'Home URL', 'cs-wppb' ),
								'label_export' => 'Home URL',
								'value'        => get_home_url(),
							),
							array(
								'label'        => esc_html__( 'Site URL', 'cs-wppb' ),
								'label_export' => 'Site URL',
								'value'        => get_site_url(),
							),
							array(
								'label'        => esc_html__( 'WordPress Version', 'cs-wppb' ),
								'label_export' => 'WordPress Version',
								'value'        => $wp_version,
								'type'         => 'wordpress_version_check',
								'versions'     => array(
									'support' => array(
										'version_compare' => '>=',
										'minimum_version' => CS_WPPB_MIN_WP_VERSION_SUPPORT_TERMS,
										'validation_message' => sprintf(
											esc_html__( 'The cs-wppb support agreement requires WordPress %s or greater. This site must be upgraded in order to be eligible for support.', 'cs-wppb' ),
											CS_WPPB_MIN_WP_VERSION_SUPPORT_TERMS
										),
									),
									'minimum' => array(
										'version_compare' => '>=',
										'minimum_version' => CS_WPPB_MIN_WP_VERSION,
										'validation_message' => sprintf(
											esc_html__( 'cs-wppb requires WordPress %s or greater. You must upgrade WordPress in order to use cs-wppb.', 'cs-wppb' ),
											CS_WPPB_MIN_WP_VERSION
										),
									),
								),
							),
							array(
								'label'        => esc_html__( 'WordPress Multisite', 'cs-wppb' ),
								'label_export' => 'WordPress Multisite',
								'value'        => is_multisite() ? __( 'Yes', 'cs-wppb' ) : __( 'No', 'cs-wppb' ),
								'value_export' => is_multisite() ? 'Yes' : 'No',
							),
							array(
								'label'        => esc_html__( 'WordPress Memory Limit', 'cs-wppb' ),
								'label_export' => 'WordPress Memory Limit',
								'value'        => WP_MEMORY_LIMIT,
							),
							array(
								'label'        => esc_html__( 'WordPress Debug Mode', 'cs-wppb' ),
								'label_export' => 'WordPress Debug Mode',
								'value'        => WP_DEBUG ? __( 'Yes', 'cs-wppb' ) : __( 'No', 'cs-wppb' ),
								'value_export' => WP_DEBUG ? 'Yes' : 'No',
							),
							array(
								'label'        => esc_html__( 'WordPress Debug Log', 'cs-wppb' ),
								'label_export' => 'WordPress Debug Log',
								'value'        => WP_DEBUG_LOG ? __( 'Yes', 'cs-wppb' ) : __( 'No', 'cs-wppb' ),
								'value_export' => WP_DEBUG_LOG ? 'Yes' : 'No',
							),
							array(
								'label'        => esc_html__( 'WordPress Script Debug Mode', 'cs-wppb' ),
								'label_export' => 'WordPress Script Debug Mode',
								'value'        => SCRIPT_DEBUG ? __( 'Yes', 'cs-wppb' ) : __( 'No', 'cs-wppb' ),
								'value_export' => SCRIPT_DEBUG ? 'Yes' : 'No',
							),
							array(
								'label'        => esc_html__( 'WordPress Cron', 'cs-wppb' ),
								'label_export' => 'WordPress Cron',
								'value'        => ! $wp_cron_disabled ? __( 'Yes', 'cs-wppb' ) : __( 'No', 'cs-wppb' ),
								'value_export' => ! $wp_cron_disabled ? 'Yes' : 'No',
							),
							array(
								'label'        => esc_html__( 'WordPress Alternate Cron', 'cs-wppb' ),
								'label_export' => 'WordPress Alternate Cron',
								'value'        => $alternate_wp_cron ? __( 'Yes', 'cs-wppb' ) : __( 'No', 'cs-wppb' ),
								'value_export' => $alternate_wp_cron ? 'Yes' : 'No',
							),
							array(
								'label'              => esc_html__( 'Background tasks', 'cs-wppb' ),
								'label_export'       => 'Background tasks',
								'type'               => 'wordpress_background_tasks',
								'value'              => $background_tasks ? __( 'Yes', 'cs-wppb' ) : __( 'No', 'cs-wppb' ),
								'value_export'       => $background_tasks ? 'Yes' : 'No',
								'is_valid'           => $background_tasks,
								'validation_message' => $background_validation_message,
							),
						),
					),
					array(
						'title'        => esc_html__( 'Active Theme', 'cs-wppb' ),
						'title_export' => 'Active Theme',
						'items'        => self::get_theme(),
					),
					array(
						'title'        => esc_html__( 'Active Plugins', 'cs-wppb' ),
						'title_export' => 'Active Plugins',
						'items'        => self::get_active_plugins( false, false, true ),
					),
					array(
						'title'        => esc_html__( 'Network Active Plugins', 'cs-wppb' ),
						'title_export' => 'Network Active Plugins',
						'items'        => self::get_network_active_plugins(),
					),
				),
			),
			array(
				'title'        => esc_html__( 'Server Environment', 'cs-wppb' ),
				'title_export' => 'Server Environment',
				'tables'       => array(
					array(
						'title'        => esc_html__( 'Web Server', 'cs-wppb' ),
						'title_export' => 'Web Server',
						'items'        => array(
							array(
								'label'        => esc_html__( 'Software', 'cs-wppb' ),
								'label_export' => 'Software',
								'value'        => esc_html( $_SERVER['SERVER_SOFTWARE'] ),
							),
							array(
								'label'        => esc_html__( 'Port', 'cs-wppb' ),
								'label_export' => 'Port',
								'value'        => esc_html( $_SERVER['SERVER_PORT'] ),
							),
							array(
								'label'        => esc_html__( 'Document Root', 'cs-wppb' ),
								'label_export' => 'Document Root',
								'value'        => esc_html( $_SERVER['DOCUMENT_ROOT'] ),
							),
						),
					),
					array(
						'title'        => esc_html__( 'PHP', 'cs-wppb' ),
						'title_export' => 'PHP',
						'items'        => array(
							array(
								'label'              => esc_html__( 'Version', 'cs-wppb' ),
								'label_export'       => 'Version',
								'value'              => esc_html( phpversion() ),
								'type'               => 'version_check',
								'version_compare'    => '>=',
								'minimum_version'    => CS_WPPB_MIN_PHP_VERSION,
								'validation_message' => esc_html__( 'Recommended: PHP ' . CS_WPPB_MIN_PHP_VERSION . ' or higher.', 'cs-wppb' ),
							),
							array(
								'label'        => esc_html__( 'Memory Limit', 'cs-wppb' ) . ' (memory_limit)',
								'label_export' => 'Memory Limit',
								'value'        => esc_html( ini_get( 'memory_limit' ) ),
							),
							array(
								'label'        => esc_html__( 'Maximum Execution Time', 'cs-wppb' ) . ' (max_execution_time)',
								'label_export' => 'Maximum Execution Time',
								'value'        => esc_html( ini_get( 'max_execution_time' ) ),
							),
							array(
								'label'        => esc_html__( 'Maximum File Upload Size', 'cs-wppb' ) . ' (upload_max_filesize)',
								'label_export' => 'Maximum File Upload Size',
								'value'        => esc_html( ini_get( 'upload_max_filesize' ) ),
							),
							array(
								'label'        => esc_html__( 'Maximum File Uploads', 'cs-wppb' ) . ' (max_file_uploads)',
								'label_export' => 'Maximum File Uploads',
								'value'        => esc_html( ini_get( 'max_file_uploads' ) ),
							),
							array(
								'label'        => esc_html__( 'Maximum Post Size', 'cs-wppb' ) . ' (post_max_size)',
								'label_export' => 'Maximum Post Size',
								'value'        => esc_html( ini_get( 'post_max_size' ) ),
							),
							array(
								'label'        => esc_html__( 'Maximum Input Variables', 'cs-wppb' ) . ' (max_input_vars)',
								'label_export' => 'Maximum Input Variables',
								'value'        => esc_html( ini_get( 'max_input_vars' ) ),
							),
							array(
								'label'        => esc_html__( 'cURL Enabled', 'cs-wppb' ),
								'label_export' => 'cURL Enabled',
								'value'        => function_exists( 'curl_init' ) ? __( 'Yes', 'cs-wppb' ) . ' (' . __( 'version', 'cs-wppb' ) . ' ' . Helpers::rgar( curl_version(), 'version' ) . ')' : __( 'No', 'cs-wppb' ),
								'value_export' => function_exists( 'curl_init' ) ? 'Yes' . ' (' . __( 'version', 'cs-wppb' ) . ' ' . Helpers::rgar( curl_version(), 'version' ) . ')' : 'No',
							),
							array(
								'label'        => esc_html__( 'OpenSSL', 'cs-wppb' ),
								'label_export' => 'OpenSSL',
								'value'        => defined( 'OPENSSL_VERSION_TEXT' ) ? OPENSSL_VERSION_TEXT . ' (' . OPENSSL_VERSION_NUMBER . ')' : __( 'No', 'cs-wppb' ),
								'value_export' => defined( 'OPENSSL_VERSION_TEXT' ) ? OPENSSL_VERSION_TEXT . ' (' . OPENSSL_VERSION_NUMBER . ')' : 'No',
							),
							array(
								'label'        => esc_html__( 'Mcrypt Enabled', 'cs-wppb' ),
								'label_export' => 'Mcrypt Enabled',
								'value'        => function_exists( 'mcrypt_encrypt' ) ? __( 'Yes', 'cs-wppb' ) : __( 'No', 'cs-wppb' ),
								'value_export' => function_exists( 'mcrypt_encrypt' ) ? 'Yes' : 'No',
							),
							array(
								'label'        => esc_html__( 'Mbstring Enabled', 'cs-wppb' ),
								'label_export' => 'Mbstring Enabled',
								'value'        => function_exists( 'mb_strlen' ) ? __( 'Yes', 'cs-wppb' ) : __( 'No', 'cs-wppb' ),
								'value_export' => function_exists( 'mb_strlen' ) ? 'Yes' : 'No',
							),
							array(
								'label'        => esc_html__( 'Loaded Extensions', 'cs-wppb' ),
								'label_export' => 'Loaded Extensions',
								'type'         => 'csv',
								'value'        => get_loaded_extensions(),
							),
						),
					),
					array(
						'title'        => esc_html__( 'MySQL', 'cs-wppb' ),
						'title_export' => 'MySQL',
						'items'        => array(
							array(
								'label'              => esc_html__( 'Version', 'cs-wppb' ),
								'label_export'       => 'Version',
								'value'              => esc_html( $wpdb->db_version() ),
								'type'               => 'version_check',
								'version_compare'    => '>',
								'minimum_version'    => CS_WPPB_MIN_MYSQL_VERSION,
								'validation_message' => esc_html__( 'cs-wppb requires MySQL ' . CS_WPPB_MIN_MYSQL_VERSION . ' or above.', 'cs-wppb' ),
							),
							array(
								'label'        => esc_html__( 'Database Character Set', 'cs-wppb' ),
								'label_export' => 'Database Character Set',
								'value'        => esc_html( $wpdb->get_var( 'SELECT @@character_set_database' ) ),
							),
							array(
								'label'        => esc_html__( 'Database Collation', 'cs-wppb' ),
								'label_export' => 'Database Collation',
								'value'        => esc_html( $wpdb->get_var( 'SELECT @@collation_database' ) ),
							),
						),
					),
				),
			),
		);

		/**
		 * Modify sections displayed on the System Status page.
		 *
		 * @since  1.0.1
		 *
		 * @param array $system_status An array of default sections displayed on the System Status page.
		 */
		$system_report = apply_filters( 'cs_wppb_system_report', $system_report );

		return $system_report;

	}

	/**
	 * Prepare item value for System Status table.
	 *
	 * @since  1.0.1
	 * @access public
	 *
	 * @param array $item System Status item.
	 *
	 * @uses SystemReport::get_export()
	 *
	 * @return string
	 */
	public static function prepare_item_value( $item, $is_export = false ) {

		// Get display as type.
		$type = Helpers::rgar( $item, 'type' );

		// Prepare value.
		switch ( $type ) {

			case 'csv':
				return implode( ', ', $item['value'] );

			case 'version_check':
				// Is the provided value a valid version?
				$valid_version = version_compare( $item['value'], $item['minimum_version'], $item['version_compare'] );

				// Display value based on valid version check.
				if ( $valid_version ) {
					return $is_export ? self::get_export( $item, 'value' ) . ' ✔' : $item['value'] . ' <mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';

				} elseif ( $is_export ) {
					$html = self::get_export( $item, 'value' ) . ' ✘ ' . self::get_export( $item, 'validation_message' );

					return $html;

				} else {
					$html  = $item['value'] . ' <mark class="error"><span class="dashicons dashicons-no"></span></mark>';
					$html .= '<span class="error_message">' . Helpers::rgar( $item, 'validation_message' ) . '</span>';

					return $html;
				}

			case 'wordpress_version_check':
				// Run version checks.
				$version_check_support = version_compare( $item['value'], $item['versions']['support']['minimum_version'], $item['versions']['support']['version_compare'] );
				$version_check_min     = version_compare( $item['value'], $item['versions']['minimum']['minimum_version'], $item['versions']['minimum']['version_compare'] );

				// If minimum WordPress version for support passed, return valid state.
				if ( $version_check_support ) {
					return $is_export ? self::get_export( $item, 'value' ) . ' ✔' : $item['value'] . ' <mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';

				} elseif ( $is_export ) {

					$validation_message = $version_check_min ? self::get_export( $item['versions']['support'], 'validation_message' ) : self::get_export( $item['versions']['minimum'], 'validation_message' );

					return self::get_export( $item, 'value' ) . ' ✘ ' . $validation_message;

				} else {

					$validation_message = $version_check_min ? $item['versions']['support']['validation_message'] : $item['versions']['minimum']['validation_message'];

					$html  = $item['value'] . ' <mark class="error"><span class="dashicons dashicons-no"></span></mark> ';
					$html .= '<span class="error_message">' . $validation_message . '</span>';

					return $html;
				}

			default:
				$value = $is_export ? self::get_export( $item, 'value' ) : Helpers::rgar( $item, 'value' );

				if ( Helpers::rgar( $item, 'is_valid' ) ) {

					$value .= $is_export ? '  ✔' : '&nbsp;<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';

					if ( ! Helpers::rgempty( 'message', $item ) ) {
						$value .= $is_export ? ' ' . self::get_export( $item, 'message' ) : '&nbsp;' . Helpers::rgar( $item, 'message' );
					}
				} elseif ( Helpers::rgar( $item, 'is_valid' ) === false ) {

					$value .= $is_export ? ' ✘' : '&nbsp;<mark class="error"><span class="dashicons dashicons-no"></span></mark>';

					if ( ! Helpers::rgempty( 'validation_message', $item ) ) {
						$value .= $is_export ? ' ' . self::get_export( $item, 'validation_message' ) : '&nbsp;<span class="error_message">' . Helpers::rgar( $item, 'validation_message' ) . '</span>';
					}
				}

				return $value;

		}

	}


	/**
	 * Get active plugins for system report.
	 *
	 * @since  1.0.1
	 * @access public
	 *
	 * @param bool $include_gravity_forms  Include Gravity Forms in plugin list.
	 * @param bool $include_cs_wppb_addons      Include Add-On Framework plugins in plugin list.
	 * @param bool $included_non_cs_wppb_addons Include non Add-On Framework plugins in plugin list.
	 *
	 * @uses Common::get_version_info()
	 * @uses SystemReport::get_cs_wppb_addon()
	 *
	 * @return string
	 */
	public static function get_active_plugins( $include_gravity_forms = true, $include_cs_wppb_addons = true, $include_non_cs_wppb_addons = true ) {

		// Initialize active plugins array.
		$active_plugins = array();

		// Get Gravity Forms version info.
		$version_info = Common::get_version_info();

		// Prepare active plugins.
		foreach ( get_plugins() as $plugin_path => $plugin ) {

			// If plugin is not active, skip it.
			if ( ! is_plugin_active( $plugin_path ) ) {
				continue;
			}

			// If this plugin is Gravity Forms and it is not to be included, skip it.
			// if ( 'gravityforms/gravityforms.php' === $plugin_path && ! $include_gravity_forms ) {
			// continue;
			// }

			// Check if plugin is a Gravity Forms Add-On.
			// $addon    = self::get_cs_wppb_addon( $plugin_path );
			// $is_addon = $addon !== false;

			// If this plugin is an Add-On and Add-Ons are not to be included, skip it.
			// if ( $is_addon && ! $include_cs_wppb_addons ) {
			// continue;
			// }

			// If this plugin is not an Add-On and non Add-Ons are not to be included, skip it.
			// if ( ! $is_addon && ! $include_non_cs_wppb_addons ) {
			// continue;
			// }

			// Define default validity and error message.
			$is_valid                  = true;
			$validation_message        = '';
			$validation_message_export = '';

			// If plugin is an Add-On, check for available updates.
			// if ( $is_addon ) {

			// Get plugin slug.
			// $slug = $addon->get_slug();

			// $minimum_requirements = $addon->meets_minimum_requirements();

			// If the Add-On is an official Add-On and an update exists, add "error" message.
			// if ( isset( $version_info['offerings'][ $slug ] ) && version_compare( $plugin['Version'], $version_info['offerings'][ $slug ]['version'], '<' ) ) {

			// $is_valid           = false;
			// $validation_message = sprintf( __( 'New version %s available.', 'cs-wppb' ), $version_info['offerings'][ $slug ]['version'] );

			// } elseif ( ! $minimum_requirements['meets_requirements'] ) {

			// $errors                    = $minimum_requirements['errors'];
			// $is_valid                  = false;
			// $validation_message        = sprintf( __( 'Your system does not meet the minimum requirements for this Add-On (%d errors).', 'cs-wppb' ), count( $errors ) );
			// $validation_message_export = sprintf( 'Your system does not meet the minimum requirements for this Add-On (%1$d errors). %2$s', count( $errors ), implode( '. ', $errors ) );

			// }
			// }

			// Cleaning up Add-On name
			// $plugin_name = $is_addon ? str_replace( ' Add-On', '', str_replace( 'Gravity Forms ', '', $plugin['Name'] ) ) : $plugin['Name'];
			$plugin_name = $plugin['Name'];

			// Prepare plugin label.
			if ( Helpers::rgar( $plugin, 'PluginURI' ) ) {
				$label = '<a href="' . esc_url( $plugin['PluginURI'] ) . '">' . esc_html( $plugin_name ) . '</a>';
			} else {
				$label = esc_html( $plugin_name );
			}

			// Prepare plugin value.
			if ( Helpers::rgar( $plugin, 'AuthorURI' ) ) {
				$value = 'by <a href="' . esc_url( $plugin['AuthorURI'] ) . '">' . esc_html( $plugin['Author'] ) . '</a>' . ' - ' . $plugin['Version'];
			} else {
				$value = 'by ' . $plugin['Author'] . ' - ' . $plugin['Version'];
			}

			// Add plugin to active plugins.
			$active_plugins[] = array(
				'label'                     => $label,
				'label_export'              => strip_tags( $plugin_name ),
				'value'                     => $value,
				'value_export'              => 'by ' . strip_tags( $plugin['Author'] ) . ' - ' . $plugin['Version'],
				'is_valid'                  => $is_valid,
				'validation_message'        => $validation_message,
				'validation_message_export' => $validation_message_export,
			);

		}

		return $active_plugins;

	}

	/**
	 * Get network active plugins for system report.
	 *
	 * @since  1.0.1
	 * @access public
	 *
	 * @uses wpdb::get_var()
	 * @uses wpdb::prepare()
	 *
	 * @return string
	 */
	public static function get_network_active_plugins() {

		global $wpdb;

		// If multi-site is not active, return.
		if ( ! is_multisite() ) {
			return;
		}

		// Get network active plugins.
		$network_active_plugins = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM {$wpdb->sitemeta} WHERE meta_key=%s", 'active_sitewide_plugins' ) );

		// If no network active plugins were found, return.
		if ( empty( $network_active_plugins ) ) {
			return;
		}

		// Convert network active plugins to array.
		$network_active_plugins = maybe_unserialize( $network_active_plugins );

		// Loop through network active plugins.
		foreach ( $network_active_plugins as $plugin_path => &$plugin ) {

			// Get plugin data.
			$plugin_data = get_plugin_data( WP_CONTENT_DIR . '/plugins/' . $plugin_path );

			// Prepare plugin label.
			if ( Helpers::rgar( $plugin_data, 'PluginURI' ) ) {
				$label = '<a href="' . esc_url( $plugin_data['PluginURI'] ) . '">' . esc_html( $plugin_data['Name'] ) . '</a>';
			} else {
				$label = esc_html( $plugin_data['Name'] );
			}

			// Prepare plugin value.
			if ( Helpers::rgar( $plugin_data, 'AuthorURI' ) ) {
				$value = 'by <a href="' . esc_url( $plugin_data['AuthorURI'] ) . '">' . $plugin_data['Author'] . '</a>' . ' - ' . $plugin_data['Version'];
			} else {
				$value = 'by ' . $plugin_data['Author'] . ' - ' . $plugin_data['Version'];
			}

			// Replace plugin.
			$plugin = array(
				'label'        => $label,
				'label_export' => strip_tags( $label ),
				'value'        => $value,
				'value_export' => strip_tags( $value ),
			);

		}

		// Convert active plugins to string.
		return $network_active_plugins;

	}


	/**
	 * Get the theme info.
	 *
	 * @since  2.2.5.9
	 * @access public
	 *
	 * @return array
	 */
	public static function get_theme() {

		wp_update_themes();
		$update_themes          = get_site_transient( 'update_themes' );
		$update_themes_versions = ! empty( $update_themes->checked ) ? $update_themes->checked : array();

		$active_theme     = wp_get_theme();
		$theme_name       = wp_strip_all_tags( $active_theme->get( 'Name' ) );
		$theme_version    = wp_strip_all_tags( $active_theme->get( 'Version' ) );
		$theme_author     = wp_strip_all_tags( $active_theme->get( 'Author' ) );
		$theme_author_uri = esc_url( $active_theme->get( 'AuthorURI' ) );

		$theme_details = array(
			array(
				'label'        => $theme_name,
				'value'        => sprintf( 'by <a href="%s">%s</a> - %s', $theme_author_uri, $theme_author, $theme_version ),
				'value_export' => sprintf( 'by %s (%s) - %s', $theme_author, $theme_author_uri, $theme_version ),
				'is_valid'     => version_compare( $theme_version, Helpers::rgar( $update_themes_versions, $active_theme->get_stylesheet() ), '>=' ),
			),
		);

		if ( is_child_theme() ) {
			$parent_theme      = wp_get_theme( $active_theme->get( 'Template' ) );
			$parent_name       = wp_strip_all_tags( $parent_theme->get( 'Name' ) );
			$parent_version    = wp_strip_all_tags( $parent_theme->get( 'Version' ) );
			$parent_author     = wp_strip_all_tags( $parent_theme->get( 'Author' ) );
			$parent_author_uri = esc_url( $parent_theme->get( 'AuthorURI' ) );

			$theme_details[] = array(
				'label'        => sprintf( '%s (%s)', $parent_name, esc_html__( 'Parent', 'cs-wppb' ) ),
				'label_export' => $parent_name . ' (Parent)',
				'value'        => sprintf( 'by <a href="%s">%s</a> - %s', $parent_author_uri, $parent_author, $parent_version ),
				'value_export' => sprintf( 'by %s (%s) - %s', $parent_author, $parent_author_uri, $parent_version ),
				'is_valid'     => version_compare( $parent_version, Helpers::rgar( $update_themes_versions, $parent_theme->get_stylesheet() ), '>=' ),
			);
		}

		return $theme_details;

	}

}
