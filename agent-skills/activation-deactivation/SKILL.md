---
name: activation-deactivation
description: Handle plugin activation, deactivation, and uninstall. Use when setting up database tables, default options, cleanup tasks, or requirement checks.
---

# Activation & Deactivation

Handle plugin lifecycle events.

## Activation

**File**: `includes/core/class-activator.php`

```php
<?php

namespace WPPB\Core;

use WPPB\Traits\RequirementChecksTrait;

defined( 'ABSPATH' ) || exit;

class Activator {

	use RequirementChecksTrait;

	public static function activate(): void {
		$config = array(
			'MIN_WP_VERSION'  => '5.8',
			'MIN_PHP_VERSION' => '7.4',
		);

		// Check requirements - throws \Exception on failure
		// WordPress auto-deactivates plugin if activation fails
		self::run_requirement_checks( $config );

		// Create database tables
		self::create_tables();

		// Set default options
		self::set_default_options();

		// Flush rewrite rules
		self::flush_rewrite_rules();

		// Set activation flag
		update_option( 'wppb_activated', true );
		update_option( 'wppb_version', '1.0.0' );
	}

	private static function create_tables(): void {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
		$table_name      = $wpdb->prefix . 'wppb_data';

		$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			name varchar(255) NOT NULL,
			value text NOT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id)
		) {$charset_collate};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	private static function set_default_options(): void {
		$defaults = array(
			'wppb_setting_1' => 'default_value',
			'wppb_setting_2' => true,
			'wppb_setting_3' => array( 'option1', 'option2' ),
		);

		foreach ( $defaults as $key => $value ) {
			if ( false === get_option( $key ) ) {
				add_option( $key, $value );
			}
		}
	}

	private static function flush_rewrite_rules(): void {
		// Register custom post types/taxonomies first
		// Then flush
		flush_rewrite_rules();
	}
}
```

## Deactivation

**File**: `includes/core/class-deactivator.php`

```php
<?php

namespace WPPB\Core;

defined( 'ABSPATH' ) || exit;

class Deactivator {

	public static function deactivate(): void {
		// Flush rewrite rules
		flush_rewrite_rules();

		// Clear scheduled events
		self::clear_scheduled_events();

		// Clear transients
		self::clear_transients();

		// Set deactivation flag
		update_option( 'wppb_deactivated', true );
	}

	private static function clear_scheduled_events(): void {
		$timestamp = wp_next_scheduled( 'wppb_daily_task' );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, 'wppb_daily_task' );
		}
	}

	private static function clear_transients(): void {
		delete_transient( 'wppb_cache_key' );
		delete_transient( 'wppb_api_data' );
	}
}
```

## Uninstall

**File**: `uninstall.php` (root directory)

```php
<?php
/**
 * Uninstall script
 *
 * @package WPPB
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Load autoloaders if using Uninstaller class
require_once __DIR__ . '/vendor/autoload.php';
\WPPB\Autoloader::register( __DIR__ );

// Delete options
delete_option( 'wppb_setting_1' );
delete_option( 'wppb_setting_2' );
delete_option( 'wppb_setting_3' );
delete_option( 'wppb_activated' );
delete_option( 'wppb_version' );

// Delete transients
delete_transient( 'wppb_cache_key' );

// Delete user meta
delete_metadata( 'user', 0, 'wppb_user_setting', '', true );

// Drop custom tables
global $wpdb;
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wppb_data" );

// Clear scheduled events
wp_clear_scheduled_hook( 'wppb_daily_task' );
```

## Register Hooks

**File**: `index.php`

```php
<?php

// Load autoloaders BEFORE hooks
require_once __DIR__ . '/vendor/autoload.php';
\WPPB\Autoloader::register( __DIR__ );

register_activation_hook( __FILE__, array( \WPPB\Core\Activator::class, 'activate' ) );
register_deactivation_hook( __FILE__, array( \WPPB\Core\Deactivator::class, 'deactivate' ) );
```

## Requirement Checks

```php
use WPPB\Traits\RequirementChecksTrait;

class Activator {
	use RequirementChecksTrait;

	public static function activate(): void {
		$config = array(
			'MIN_WP_VERSION'  => '5.8',
			'MIN_PHP_VERSION' => '7.4',
		);

		// Throws \Exception if requirements not met
		// WordPress auto-deactivates plugin on activation failure
		self::run_requirement_checks( $config );
	}
}
```

## Database Migrations

```php
private static function maybe_upgrade(): void {
	$current_version = get_option( 'wppb_version', '0.0.0' );
	$new_version     = '1.1.0';

	if ( version_compare( $current_version, $new_version, '<' ) ) {
		self::upgrade_to_1_1_0();
		update_option( 'wppb_version', $new_version );
	}
}

private static function upgrade_to_1_1_0(): void {
	global $wpdb;

	$table_name = $wpdb->prefix . 'wppb_data';

	// Add new column
	$wpdb->query(
		"ALTER TABLE {$table_name} 
		ADD COLUMN status varchar(20) DEFAULT 'active' 
		AFTER value"
	);
}
```

## Scheduled Events

### Schedule on Activation

```php
private static function schedule_events(): void {
	if ( ! wp_next_scheduled( 'wppb_daily_task' ) ) {
		wp_schedule_event(
			time(),
			'daily',
			'wppb_daily_task'
		);
	}
}
```

### Clear on Deactivation

```php
private static function clear_scheduled_events(): void {
	wp_clear_scheduled_hook( 'wppb_daily_task' );
}
```

## Multisite Support

```php
public static function activate( bool $network_wide ): void {
	if ( is_multisite() && $network_wide ) {
		global $wpdb;

		foreach ( $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" ) as $blog_id ) {
			switch_to_blog( $blog_id );
			self::single_activate();
			restore_current_blog();
		}
	} else {
		self::single_activate();
	}
}

private static function single_activate(): void {
	self::create_tables();
	self::set_default_options();
}
```

## Activation Redirect

```php
public static function activate(): void {
	// Set redirect flag
	set_transient( 'wppb_activation_redirect', true, 30 );
}
```

In admin init:

```php
public function maybe_redirect(): void {
	if ( get_transient( 'wppb_activation_redirect' ) ) {
		delete_transient( 'wppb_activation_redirect' );

		wp_safe_redirect(
			admin_url( 'admin.php?page=wppb-welcome' )
		);
		exit;
	}
}
```

## Rules

- Use Activator class for activation logic
- Use Deactivator class for deactivation logic
- Use uninstall.php for cleanup (not deactivation)
- Check requirements on activation
- Create database tables with dbDelta()
- Set default options on activation
- Flush rewrite rules on activation/deactivation
- Clear scheduled events on deactivation
- Delete all data on uninstall
- Never use deactivation for data cleanup
- Use transients for activation redirects

