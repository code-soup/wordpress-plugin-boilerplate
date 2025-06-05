<?php

declare(strict_types=1);

namespace WPPB\Core;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Plugin Lifecycle Manager
 *
 * Handles plugin activation, deactivation, uninstallation, and version upgrades.
 * Provides hooks for other components to register lifecycle callbacks.
 *
 * @since 1.0.0
 */
class Lifecycle {

	use \WPPB\Traits\HelpersTrait;
	use \WPPB\Traits\LoggingTrait;

	/**
	 * Plugin version option key
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private const VERSION_OPTION = 'wppb_version';

	/**
	 * Plugin options key
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private const OPTIONS_KEY = 'wppb_options';

	/**
	 * Activation callbacks
	 *
	 * @var array<callable>
	 * @since 1.0.0
	 */
	private array $activation_callbacks = array();

	/**
	 * Deactivation callbacks
	 *
	 * @var array<callable>
	 * @since 1.0.0
	 */
	private array $deactivation_callbacks = array();

	/**
	 * Upgrade callbacks
	 *
	 * @var array<string, callable>
	 * @since 1.0.0
	 */
	private array $upgrade_callbacks = array();

	/**
	 * Handle plugin activation
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function activate(): void {
		try {
			$this->info( 'Plugin activation started' );

			// Check WordPress and PHP version requirements
			$this->check_requirements();

			// Get current and previous versions
			$current_version  = $this->get_plugin_version();
			$previous_version = get_option( self::VERSION_OPTION, '0.0.0' );

			// Run activation callbacks
			foreach ( $this->activation_callbacks as $callback ) {
				if ( is_callable( $callback ) ) {
					call_user_func( $callback );
				}
			}

			// Handle version upgrade if needed
			if ( version_compare( $previous_version, $current_version, '<' ) ) {
				$this->handle_upgrade( $previous_version, $current_version );
			}

			// Create default options
			$this->create_default_options();

			// Update version
			update_option( self::VERSION_OPTION, $current_version );

			// Set activation timestamp
			update_option( 'wppb_activated_time', time() );

			// Flush rewrite rules
			flush_rewrite_rules();

			$this->info( "Plugin activated successfully. Version: {$current_version}" );

		} catch ( \Exception $e ) {
			$this->log_exception( $e );

			// Deactivate plugin on error
			deactivate_plugins( plugin_basename( $this->get_plugin_dir_path() . '/plugin.php' ) );

			wp_die(
				esc_html( $e->getMessage() ),
				esc_html__( 'Plugin Activation Error', 'wppb' ),
				array( 'back_link' => true )
			);
		}
	}

	/**
	 * Handle plugin deactivation
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function deactivate(): void {
		try {
			$this->info( 'Plugin deactivation started' );

			// Run deactivation callbacks
			foreach ( $this->deactivation_callbacks as $callback ) {
				if ( is_callable( $callback ) ) {
					call_user_func( $callback );
				}
			}

			// Clear caches
			$this->clear_caches();

			// Set deactivation timestamp
			update_option( 'wppb_deactivated_time', time() );

			// Flush rewrite rules
			flush_rewrite_rules();

			$this->info( 'Plugin deactivated successfully' );

		} catch ( \Exception $e ) {
			$this->log_exception( $e );
		}
	}

	/**
	 * Handle plugin uninstallation
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function uninstall(): void {
		try {
			$this->info( 'Plugin uninstallation started' );

			// Remove all plugin options
			delete_option( self::VERSION_OPTION );
			delete_option( self::OPTIONS_KEY );
			delete_option( 'wppb_activated_time' );
			delete_option( 'wppb_deactivated_time' );

			// Clear all caches and transients
			$this->clear_all_data();

			$this->info( 'Plugin uninstalled successfully' );

		} catch ( \Exception $e ) {
			$this->log_exception( $e );
		}
	}

	/**
	 * Register activation callback
	 *
	 * @since 1.0.0
	 * @param callable $callback Callback function
	 * @return self
	 */
	public function on_activation( callable $callback ): self {
		$this->activation_callbacks[] = $callback;
		return $this;
	}

	/**
	 * Register deactivation callback
	 *
	 * @since 1.0.0
	 * @param callable $callback Callback function
	 * @return self
	 */
	public function on_deactivation( callable $callback ): self {
		$this->deactivation_callbacks[] = $callback;
		return $this;
	}

	/**
	 * Register upgrade callback for specific version
	 *
	 * @since 1.0.0
	 * @param string $version Target version
	 * @param callable $callback Callback function
	 * @return self
	 */
	public function on_upgrade( string $version, callable $callback ): self {
		$this->upgrade_callbacks[ $version ] = $callback;
		return $this;
	}

	/**
	 * Check WordPress and PHP version requirements
	 *
	 * @since 1.0.0
	 * @return void
	 * @throws \RuntimeException If requirements are not met
	 */
	private function check_requirements(): void {
		global $wp_version;

		// Check WordPress version
		$min_wp_version = '5.0';
		if ( version_compare( $wp_version, $min_wp_version, '<' ) ) {
			throw new \RuntimeException(
				sprintf(
					'This plugin requires WordPress %s or higher. You are running %s.',
					$min_wp_version,
					$wp_version
				)
			);
		}

		// Check PHP version
		$min_php_version = '8.0';
		if ( version_compare( PHP_VERSION, $min_php_version, '<' ) ) {
			throw new \RuntimeException(
				sprintf(
					'This plugin requires PHP %s or higher. You are running %s.',
					$min_php_version,
					PHP_VERSION
				)
			);
		}
	}

	/**
	 * Handle version upgrade
	 *
	 * @since 1.0.0
	 * @param string $from_version Previous version
	 * @param string $to_version Current version
	 * @return void
	 */
	private function handle_upgrade( string $from_version, string $to_version ): void {
		$this->info( "Upgrading from version {$from_version} to {$to_version}" );

		// Run version-specific upgrade callbacks
		foreach ( $this->upgrade_callbacks as $version => $callback ) {
			if ( version_compare( $from_version, $version, '<' ) &&
				version_compare( $to_version, $version, '>=' ) ) {

				$this->info( "Running upgrade callback for version {$version}" );

				if ( is_callable( $callback ) ) {
					call_user_func( $callback, $from_version, $to_version );
				}
			}
		}

		$this->info( 'Upgrade completed successfully' );
	}

	/**
	 * Create default plugin options
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function create_default_options(): void {
		$default_options = array(
			'version'  => $this->get_plugin_version(),
			'settings' => array(
				'enabled'    => true,
				'debug_mode' => false,
			),
			'features' => array(
				'admin_enhancements' => true,
				'frontend_features'  => true,
			),
		);

		// Only add if doesn't exist
		add_option( self::OPTIONS_KEY, $default_options );
	}

	/**
	 * Clear plugin caches
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function clear_caches(): void {
		// Clear assets manifest cache
		delete_transient( 'wppb_assets_manifest' );

		// Clear any other plugin-specific transients
		$this->clear_plugin_transients();
	}

	/**
	 * Clear all plugin data
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function clear_all_data(): void {
		$this->clear_caches();
		$this->clear_plugin_transients();
	}

	/**
	 * Clear all plugin-specific transients
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function clear_plugin_transients(): void {
		global $wpdb;

		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
				'_transient_wppb_%'
			)
		);

		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
				'_transient_timeout_wppb_%'
			)
		);
	}
}
