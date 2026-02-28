# Plugin Activation and Deactivation

This guide explains how to handle plugin activation and deactivation events in the WordPress Plugin Boilerplate.

## Overview

WordPress provides hooks that fire when a plugin is activated or deactivated. The boilerplate uses dedicated classes to handle these events:

- **Activator** (`includes/core/class-activator.php`) - Handles activation logic
- **Deactivator** (`includes/core/class-deactivator.php`) - Handles deactivation logic

---

## Activation Hooks Location

**Important**: Activation and deactivation hooks must be registered in the main plugin file (`index.php`), not in included files. WordPress may silently fail if hooks are registered elsewhere.

**File**: `index.php`

```php
<?php
// The code that runs during plugin activation.
register_activation_hook(
	__FILE__,
	function () {
		\WPPB\Core\Activator::activate();
	}
);

// The code that runs during plugin deactivation.
register_deactivation_hook(
	__FILE__,
	function () {
		\WPPB\Core\Deactivator::deactivate();
	}
);
```

---

## Plugin Activation

### Default Activator

The default `Activator` class performs requirement checks during activation.

**File**: `includes/core/class-activator.php`

```php
<?php

namespace WPPB\Core;

use WPPB\Traits\RequirementChecksTrait;
use function WPPB\plugin;

class Activator {

	use RequirementChecksTrait;

	public static function activate(): void {
		$plugin = plugin();
		self::run_requirement_checks( $plugin->config );
	}
}
```

### Requirement Checks

The `RequirementChecksTrait` validates:
- Minimum WordPress version
- Minimum PHP version

If requirements are not met, the plugin is automatically deactivated with an error message.

**Configuration** is set in `run.php`:

```php
$config = array(
	'MIN_WP_VERSION'  => '5.8',
	'MIN_PHP_VERSION' => '7.4',
	'PLUGIN_BASENAME' => plugin_basename( __FILE__ ),
);
```

---

## Adding Custom Activation Logic

### Method 1: Extend the Activator Class

Add your activation logic directly to the `Activator` class:

```php
<?php

namespace WPPB\Core;

use WPPB\Traits\RequirementChecksTrait;
use function WPPB\plugin;

class Activator {

	use RequirementChecksTrait;

	public static function activate(): void {
		$plugin = plugin();
		self::run_requirement_checks( $plugin->config );

		// Add custom activation logic
		self::create_database_tables();
		self::set_default_options();
		self::schedule_cron_events();
		self::flush_rewrite_rules();
	}

	private static function create_database_tables(): void {
		global $wpdb;
		$table_name      = $wpdb->prefix . 'wppb_custom_table';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL,
			data longtext NOT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY user_id (user_id)
		) {$charset_collate};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	private static function set_default_options(): void {
		$defaults = array(
			'wppb_version'        => '1.0.0',
			'wppb_enable_feature' => true,
			'wppb_api_key'        => '',
		);

		foreach ( $defaults as $key => $value ) {
			if ( false === get_option( $key ) ) {
				add_option( $key, $value );
			}
		}
	}

	private static function schedule_cron_events(): void {
		if ( ! wp_next_scheduled( 'wppb_daily_cleanup' ) ) {
			wp_schedule_event(
				time(),
				'daily',
				'wppb_daily_cleanup'
			);
		}
	}

	private static function flush_rewrite_rules(): void {
		// Register custom post types or rewrite rules first
		// Then flush
		flush_rewrite_rules();
	}
}
```

---

### Method 2: Use Database Schema Classes

For better organization, create dedicated schema classes:

**File**: `includes/database/class-custom-table-schema.php`

```php
<?php

namespace WPPB\Database;

defined( 'ABSPATH' ) || exit;

class CustomTableSchema {

	public static function create(): void {
		global $wpdb;
		$table_name      = $wpdb->prefix . 'wppb_custom_table';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL,
			data longtext NOT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY user_id (user_id)
		) {$charset_collate};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		// Store database version
		update_option( 'wppb_db_version', '1.0' );
	}

	public static function drop(): void {
		global $wpdb;
		$table_name = $wpdb->prefix . 'wppb_custom_table';
		$wpdb->query( "DROP TABLE IF EXISTS {$table_name}" );
	}
}
```

**Use in Activator**:

```php
<?php

namespace WPPB\Core;

use WPPB\Database\CustomTableSchema;
use WPPB\Traits\RequirementChecksTrait;
use function WPPB\plugin;

class Activator {

	use RequirementChecksTrait;

	public static function activate(): void {
		$plugin = plugin();
		self::run_requirement_checks( $plugin->config );

		// Create database tables
		CustomTableSchema::create();
	}
}
```

---

## Plugin Deactivation

### Default Deactivator

The default `Deactivator` class is minimal:

**File**: `includes/core/class-deactivator.php`

```php
<?php

namespace WPPB\Core;

defined( 'ABSPATH' ) || exit;

class Deactivator {

	public static function deactivate(): void {
		// Flush rewrite rules if needed
	}
}
```

### Adding Deactivation Logic

```php
<?php

namespace WPPB\Core;

defined( 'ABSPATH' ) || exit;

class Deactivator {

	public static function deactivate(): void {
		self::clear_scheduled_events();
		self::flush_rewrite_rules();
		self::clear_transients();
	}

	private static function clear_scheduled_events(): void {
		$timestamp = wp_next_scheduled( 'wppb_daily_cleanup' );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, 'wppb_daily_cleanup' );
		}
	}

	private static function flush_rewrite_rules(): void {
		flush_rewrite_rules();
	}

	private static function clear_transients(): void {
		global $wpdb;
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
				'_transient_wppb_%'
			)
		);
	}
}
```

---

## Plugin Uninstallation

For cleanup when the plugin is deleted (not just deactivated), use the uninstall hook.

**File**: `uninstall.php` (already exists in the boilerplate)

```php
<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package WPPB
 */

// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete options
delete_option( 'wppb_version' );
delete_option( 'wppb_enable_feature' );
delete_option( 'wppb_api_key' );

// Drop custom tables
global $wpdb;
$table_name = $wpdb->prefix . 'wppb_custom_table';
$wpdb->query( "DROP TABLE IF EXISTS {$table_name}" );

// Clear scheduled events
wp_clear_scheduled_hook( 'wppb_daily_cleanup' );

// Delete transients
$wpdb->query(
	$wpdb->prepare(
		"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
		'_transient_wppb_%',
		'_transient_timeout_wppb_%'
	)
);
```

---

## Common Activation Tasks

### 1. Creating Database Tables

Always use `dbDelta()` for creating tables:

```php
require_once ABSPATH . 'wp-admin/includes/upgrade.php';
dbDelta( $sql );
```

**Best Practices**:
- Use `IF NOT EXISTS` in CREATE TABLE statements
- Include charset and collation
- Store database version for future migrations
- Use proper indexes for performance

### 2. Setting Default Options

```php
private static function set_default_options(): void {
	$defaults = array(
		'wppb_option_1' => 'default_value',
		'wppb_option_2' => array( 'key' => 'value' ),
	);

	foreach ( $defaults as $key => $value ) {
		if ( false === get_option( $key ) ) {
			add_option( $key, $value );
		}
	}
}
```

### 3. Scheduling Cron Events

```php
private static function schedule_cron_events(): void {
	if ( ! wp_next_scheduled( 'wppb_hourly_task' ) ) {
		wp_schedule_event(
			time(),
			'hourly',
			'wppb_hourly_task'
		);
	}

	if ( ! wp_next_scheduled( 'wppb_daily_task' ) ) {
		wp_schedule_event(
			time(),
			'daily',
			'wppb_daily_task'
		);
	}
}
```

### 4. Flushing Rewrite Rules

```php
private static function flush_rewrite_rules(): void {
	// Register custom post types first
	self::register_custom_post_types();

	// Then flush
	flush_rewrite_rules();
}

private static function register_custom_post_types(): void {
	register_post_type(
		'wppb_custom',
		array(
			'public'      => true,
			'label'       => 'Custom Posts',
			'has_archive' => true,
			'rewrite'     => array( 'slug' => 'custom' ),
		)
	);
}
```

### 5. Creating Default Pages

```php
private static function create_default_pages(): void {
	$pages = array(
		array(
			'title'   => 'Dashboard',
			'slug'    => 'wppb-dashboard',
			'content' => '[wppb_dashboard]',
		),
		array(
			'title'   => 'Settings',
			'slug'    => 'wppb-settings',
			'content' => '[wppb_settings]',
		),
	);

	foreach ( $pages as $page ) {
		$existing = get_page_by_path( $page['slug'] );

		if ( ! $existing ) {
			wp_insert_post(
				array(
					'post_title'   => $page['title'],
					'post_name'    => $page['slug'],
					'post_content' => $page['content'],
					'post_status'  => 'publish',
					'post_type'    => 'page',
				)
			);
		}
	}
}
```

---

## Best Practices

### 1. Keep Activation Fast

Avoid time-consuming operations during activation:

```php
// Good - Quick operations
self::set_default_options();
self::create_database_tables();

// Avoid - Time-consuming operations
// Don't fetch external data
// Don't process large datasets
// Don't send emails
```

### 2. Handle Multisite

Check if running on multisite and handle accordingly:

```php
public static function activate(): void {
	if ( is_multisite() ) {
		self::activate_multisite();
	} else {
		self::activate_single_site();
	}
}

private static function activate_multisite(): void {
	global $wpdb;
	$blog_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );

	foreach ( $blog_ids as $blog_id ) {
		switch_to_blog( $blog_id );
		self::activate_single_site();
		restore_current_blog();
	}
}

private static function activate_single_site(): void {
	// Activation logic for single site
}
```

### 3. Version Tracking

Track plugin version for future migrations:

```php
public static function activate(): void {
	$current_version = get_option( 'wppb_version', '0.0.0' );
	$new_version     = '1.0.0';

	if ( version_compare( $current_version, $new_version, '<' ) ) {
		self::upgrade( $current_version, $new_version );
	}

	update_option( 'wppb_version', $new_version );
}

private static function upgrade( string $from, string $to ): void {
	// Handle version-specific upgrades
	if ( version_compare( $from, '0.5.0', '<' ) ) {
		// Upgrade from pre-0.5.0
	}

	if ( version_compare( $from, '1.0.0', '<' ) ) {
		// Upgrade from pre-1.0.0
	}
}
```

### 4. Error Handling

Handle errors gracefully:

```php
public static function activate(): void {
	try {
		$plugin = plugin();
		self::run_requirement_checks( $plugin->config );
		self::create_database_tables();
		self::set_default_options();
	} catch ( \Exception $e ) {
		// Log error
		error_log( 'Plugin activation failed: ' . $e->getMessage() );

		// Deactivate plugin
		deactivate_plugins( plugin_basename( __FILE__ ) );

		// Show error to user
		wp_die(
			esc_html( $e->getMessage() ),
			esc_html__( 'Plugin Activation Error', 'wppb' ),
			array( 'back_link' => true )
		);
	}
}
```

### 5. Don't Leave Data on Deactivation

Only clean up temporary data on deactivation, not user data:

```php
// Deactivation - Clean temporary data only
public static function deactivate(): void {
	self::clear_scheduled_events();
	self::clear_transients();
	flush_rewrite_rules();
	// DON'T delete user data or options
}

// Uninstallation - Clean everything
// In uninstall.php
delete_option( 'wppb_settings' );
// Drop tables
// Delete user data
```

---

## Testing Activation/Deactivation

### Manual Testing

1. Activate the plugin
2. Check that tables are created
3. Verify options are set
4. Confirm cron events are scheduled
5. Deactivate the plugin
6. Verify cleanup occurred
7. Reactivate and ensure it works again

### Automated Testing

```php
<?php
/**
 * Tests for Activator.
 *
 * @package WPPB
 */

class ActivatorTest extends WP_UnitTestCase {

	public function test_creates_database_tables(): void {
		global $wpdb;

		\WPPB\Core\Activator::activate();

		$table_name = $wpdb->prefix . 'wppb_custom_table';
		$this->assertEquals(
			$table_name,
			$wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" )
		);
	}

	public function test_sets_default_options(): void {
		\WPPB\Core\Activator::activate();

		$this->assertNotFalse( get_option( 'wppb_version' ) );
	}

	public function test_schedules_cron_events(): void {
		\WPPB\Core\Activator::activate();

		$this->assertNotFalse( wp_next_scheduled( 'wppb_daily_cleanup' ) );
	}
}
```