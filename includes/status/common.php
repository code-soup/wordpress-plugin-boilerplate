<?php

namespace wppb\status;

// Exit if accessed directly.
defined( 'WPINC' ) || die;


/**
 * Class Common
 *
 * Includes common methods accessed throughout Gravity Forms and add-ons.
 */
class Common {

	public static $errors   = array();
	public static $messages = array();

	/**
	 * An array of dismissible messages to display on the page.
	 *
	 * @var array $dismissible_messages
	 */
	public static $dismissible_messages = array();


	/**
	 * Checks for the existence of a MySQL table.
	 *
	 * @since  1.0.1
	 * @access public
	 *
	 * @param string $table_name Table to check for.
	 *
	 * @uses wpdb::get_var()
	 *
	 * @return bool
	 */
	public static function table_exists( $table_name ) {

		global $wpdb;

		$count = $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" );

		return ! empty( $count );

	}


	public static function get_version_info( $cache = true ) {

		$version_info = get_option( 'cs_wppb_version_info' );
		if ( ! $cache ) {
			$version_info = null;
		} else {

			// Checking cache expiration
			$cache_duration  = DAY_IN_SECONDS; // 24 hours.
			$cache_timestamp = $version_info && isset( $version_info['timestamp'] ) ? $version_info['timestamp'] : 0;

			// Is cache expired ?
			if ( $cache_timestamp + $cache_duration < time() ) {
				$version_info = null;
			}
		}

		if ( is_wp_error( $version_info ) || isset( $version_info['headers'] ) ) {
			// Legacy ( < 2.1.1.14 ) version info contained the whole raw response.
			$version_info = null;
		}

		if ( ! $version_info ) {
			// Getting version number
			// $options            = array( 'method' => 'POST', 'timeout' => 20 );
			// $options['headers'] = array(
			// 'Content-Type' => 'application/x-www-form-urlencoded; charset=' . get_option( 'blog_charset' ),
			// 'User-Agent'   => 'WordPress/' . get_bloginfo( 'version' ),
			// 'Referer'      => get_bloginfo( 'url' ),
			// );
			// // $options['body']    = self::get_remote_post_params();
			// $options['timeout'] = 15;

			// $nocache = $cache ? '' : 'nocache=1'; //disabling server side caching

			// $raw_response = self::post_to_manager( 'version.php', $nocache, $options );

			// if ( is_wp_error( $raw_response ) || Helpers::rgars( $raw_response, 'response/code' ) != 200 ) {

			// $version_info = array( 'is_valid_key' => '1', 'version' => '', 'url' => '', 'is_error' => '1' );
			// } else {
			// $version_info = json_decode( $raw_response['body'], true );
			// if ( empty( $version_info ) ) {
			// $version_info = array( 'is_valid_key' => '1', 'version' => '', 'url' => '', 'is_error' => '1' );
			// }
			// }

			$version_info['timestamp'] = time();

			// Caching response.
			update_option( 'cs_wppb_version_info', $version_info ); // caching version info
		}

		return $version_info;
	}


	public static function get_browser_class() {
		global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone, $post;

		$classes = array();

		// adding browser related class
		if ( $is_lynx ) {
			$classes[] = 'cs_browser_lynx';
		} elseif ( $is_gecko ) {
			$classes[] = 'cs_browser_gecko';
		} elseif ( $is_opera ) {
			$classes[] = 'cs_browser_opera';
		} elseif ( $is_NS4 ) {
			$classes[] = 'cs_browser_ns4';
		} elseif ( $is_safari ) {
			$classes[] = 'cs_browser_safari';
		} elseif ( $is_chrome ) {
			$classes[] = 'cs_browser_chrome';
		} elseif ( $is_IE ) {
			$classes[] = 'cs_browser_ie';
		} else {
			$classes[] = 'cs_browser_unknown';
		}

		// adding IE version
		if ( $is_IE ) {
			if ( strpos( $_SERVER['HTTP_USER_AGENT'], 'MSIE 6' ) !== false ) {
				$classes[] = 'cs_browser_ie6';
			} elseif ( strpos( $_SERVER['HTTP_USER_AGENT'], 'MSIE 7' ) !== false ) {
				$classes[] = 'cs_browser_ie7';
			}
			if ( strpos( $_SERVER['HTTP_USER_AGENT'], 'MSIE 8' ) !== false ) {
				$classes[] = 'cs_browser_ie8';
			}
			if ( strpos( $_SERVER['HTTP_USER_AGENT'], 'MSIE 9' ) !== false ) {
				$classes[] = 'cs_browser_ie9';
			}
		}

		if ( $is_iphone ) {
			$classes[] = 'cs_browser_iphone';
		}

		return implode( ' ', $classes );
	}


	public static function display_admin_message( $errors = false, $messages = false ) {

		if ( ! $errors ) {
			$errors = self::$errors;
		}

		if ( ! $messages ) {
			$messages = self::$messages;
		}

		$errors   = apply_filters( 'cs_wppb_admin_error_messages', $errors );
		$messages = apply_filters( 'cs_wppb_admin_messages', $messages );

		if ( ! empty( $errors ) ) {
			?>
	<div class="error below-h2">
				<?php if ( count( $errors ) > 1 ) { ?>
			<ul style="margin: 0.5em 0 0; padding: 2px;">
				<li><?php echo implode( '</li><li>', $errors ); ?></li>
			</ul>
		<?php } else { ?>
			<p><?php echo $errors[0]; ?></p>
		<?php } ?>
	</div>
			<?php
		} elseif ( ! empty( $messages ) ) {
			?>
	<div id="message" class="updated below-h2">
				<?php if ( count( $messages ) > 1 ) { ?>
			<ul style="margin: 0.5em 0 0; padding: 2px;">
				<li><?php echo implode( '</li><li>', $messages ); ?></li>
			</ul>
		<?php } else { ?>
			<p><strong><?php echo $messages[0]; ?></strong></p>
		<?php } ?>
	</div>
			<?php
		}

	}

	/**
	 * Outputs dismissible messages on the page.
	 *
	 * @param bool $messages
	 *
	 * @since  1.0.10.1
	 */
	public static function display_dismissible_message( $messages = false, $page = null ) {

		if ( ! $messages ) {
			$messages        = self::$dismissible_messages;
			$sticky_messages = get_option( 'cs_wppb_sticky_admin_messages', array() );
			$messages        = array_merge( $messages, $sticky_messages );
			$messages        = array_values( $messages );
		}

		if ( ! empty( $messages ) ) {
			foreach ( $messages as $message ) {
				if ( isset( $sticky_messages[ $message['key'] ] ) && isset( $message['page'] ) && $message['page'] && $page !== $message['page'] ) {
					continue;
				}

				if ( empty( $message['page'] ) && $page == 'site-wide' ) {
					// Prevent double display on GF pages
					continue;
				}

				if ( empty( $message['key'] ) || self::is_message_dismissed( $message['key'] ) ) {
					continue;
				}

				if ( isset( $message['capabilities'] ) && $message['capabilities'] && ! self::current_user_can_any( $message['capabilities'] ) ) {
					continue;
				}

				$class = in_array(
					$message['type'],
					array(
						'warning',
						'error',
						'updated',
						'success',
					)
				) ? $message['type'] : 'error';

			}
		}
	}

	public static function current_user_can_any( $caps ) {

		if ( ! is_array( $caps ) ) {
			$has_cap = current_user_can( $caps ) || current_user_can( 'cs_wppb_full_access' );

			return $has_cap;
		}

		foreach ( $caps as $cap ) {
			if ( current_user_can( $cap ) ) {
				return true;
			}
		}

		$has_full_access = current_user_can( 'cs_wppb_full_access' );

		return $has_full_access;
	}
}
