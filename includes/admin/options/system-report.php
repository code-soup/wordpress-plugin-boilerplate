<?php

namespace wppb\options\status;


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

		$min_wp_version    = $this->get_constant('MIN_WP_VERSION_SUPPORT_TERMS');
		$min_php_version   = $this->get_constant('MIN_PHP_VERSION');
		$min_sql_version   = $this->get_constant('MIN_MYSQL_VERSION');
		$plugin_name       = $this->get_constant('PLUGIN_NAME');

		// Report fields
		$fields = array();

		// WordPress environment
		$fields['wp'] = array(
			'title'        => esc_html__( 'WordPress Environment', 'cs-wppb' ),
			'title_export' => 'WordPress Environment',
			'tables'       => array(),
		);

		// WordPress Environment tables
		$fields['wp']['tables'][] = array(
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
						'minimum' => array(
							'version_compare' => '>=',
							'minimum_version' => $min_wp_version,
							'validation_message' => sprintf(
								esc_html__( 'Required WordPress version is %s or greater. You must upgrade WordPress in order to use %s.', 'cs-wppb' ),
								$min_wp_version,
								$plugin_name
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
			),
		);

		/* $fields['wp']['tables'][] = array(
			'title'        => esc_html__( 'Active Theme', 'cs-wppb' ),
			'title_export' => 'Active Theme',
			'items'        => self::get_theme(),
		);

		$fields['wp']['tables'][] = array(
			'title'        => esc_html__( 'Active Plugins', 'cs-wppb' ),
			'title_export' => 'Active Plugins',
			'items'        => self::get_active_plugins( false, false, true ),
		);

		$fields['wp']['tables'][] = array(
			'title'        => esc_html__( 'Network Active Plugins', 'cs-wppb' ),
			'title_export' => 'Network Active Plugins',
			'items'        => self::get_network_active_plugins(),
		);*/


		// Server environment
		$fields['server'] = array(
			'title'        => esc_html__( 'Server Environment', 'cs-wppb' ),
			'title_export' => 'Server Environment',
			'tables'       => array(),
		);

		// Web Server
		$fields['server']['tables'][] = array(
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
		);

		// PHP
		$fields['server']['tables'][] = array(
			'title'        => esc_html__( 'PHP', 'cs-wppb' ),
			'title_export' => 'PHP',
			'items'        => array(
				array(
					'label'              => esc_html__( 'Version', 'cs-wppb' ),
					'label_export'       => 'Version',
					'value'              => esc_html( phpversion() ),
					'type'               => 'version_check',
					'version_compare'    => '>=',
					'minimum_version'    => $min_php_version,
					'validation_message' => esc_html__( 'Recommended: PHP ' . $min_php_version . ' or higher.', 'cs-wppb' ),
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
		);

		// 'MySQL'
		$fields['server']['tables'][] = array(
			'title'        => esc_html__( 'MySQL', 'cs-wppb' ),
			'title_export' => 'MySQL',
			'items'        => array(
				array(
					'label'              => esc_html__( 'Version', 'cs-wppb' ),
					'label_export'       => 'Version',
					'value'              => esc_html( $wpdb->db_version() ),
					'type'               => 'version_check',
					'version_compare'    => '>',
					'minimum_version'    => $min_sql_version,
					'validation_message' => esc_html__( 'cs-wppb requires MySQL ' . $min_sql_version . ' or above.', 'cs-wppb' ),
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
		);

		return $fields;

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


}
