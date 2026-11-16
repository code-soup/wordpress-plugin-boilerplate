---
name: hooker
description: Use Hooker service to register WordPress actions and filters. Use when adding WordPress hooks, filters, or actions in the plugin. Prefer Hooker over raw add_action/add_filter in application code.
---

# Hooker Service

The Hooker service manages WordPress hooks. **Use Hooker for all application hooks** (enqueue, REST, AJAX, features).

**Lifecycle**: Queue hooks on Hooker **before** `Hooker::run()` in `Plugin::run()`. Call feature `init()` inside provider `boot()` (before `hooker->run()`).

**When Hooker cannot be used** (use raw `add_action` / `add_filter` instead):

- `index.php` activation / deactivation hooks
- `uninstall.php`
- Code that must run before the container / Hooker exists
- Rare third-party integration constraints (document the exception)

If Hooker “does not work” because hooks were queued too late, fix boot order—do not default to `add_action` in feature code.

## Get Hooker Instance

Prefer constructor injection (see admin/frontend providers). When needed ad hoc:

```php
use function WPPB\plugin;

$hooker = plugin()->get( 'hooker' );
```

## Add Action

```php
$this->hooker->add_action(
	'init',
	$this,
	'method_name',
	10,
	1
);
```

Parameters:
- Hook name
- Object instance (`$this`)
- Method name (string)
- Priority (default: 10)
- Accepted args (default: 1)

## Add Filter

```php
$this->hooker->add_filter(
	'the_content',
	$this,
	'filter_content',
	10,
	1
);
```

## Add Multiple Actions

```php
$this->hooker->add_actions(
	$this,
	array(
		'init',
		'admin_init',
		'wp_loaded',
	)
);
```

Each hook calls method with same name: `init()`, `admin_init()`, `wp_loaded()`

## Add Multiple Filters

```php
$this->hooker->add_filters(
	$this,
	array(
		'the_content',
		'the_title',
		'the_excerpt',
	)
);
```

## Bulk Registration (Advanced)

```php
$hooks = array(
	array(
		'type'     => 'action',
		'hook'     => 'init',
		'callback' => 'init_method',
		'priority' => 10,
		'args'     => 1,
	),
	array(
		'type'     => 'filter',
		'hook'     => 'the_content',
		'callback' => 'filter_content',
		'priority' => 20,
		'args'     => 1,
	),
);

$this->hooker->register( $this, $hooks );
```

## Method Omission Fallback

If method is null, hook name becomes method name:

```php
// Calls $this->init() method
$this->hooker->add_action( 'init', $this );
```

## Method Validation

Hooker validates methods exist before registering:

```php
// Throws exception if method missing
$this->hooker->add_action( 'init', $this, 'missing_method' );
// Exception: Method "missing_method" does not exist in class "YourClass"
```

## Complete Example

```php
<?php

namespace WPPB\Admin;

use function WPPB\plugin;

class SettingsPage {

	public function __construct() {
		// Constructor intentionally empty
	}

	public function init(): void {
		$hooker = plugin()->get( 'hooker' );

		// Multiple actions at once
		$hooker->add_actions(
			$this,
			array(
				'admin_menu',
				'admin_init',
			)
		);

		// Single filter with custom priority and args
		$hooker->add_filter(
			'plugin_action_links',
			$this,
			'add_settings_link',
			10,
			2
		);
	}

	public function admin_menu(): void {
		// Add menu page
	}

	public function admin_init(): void {
		// Register settings
	}

	public function add_settings_link( array $links, string $file ): array {
		// Add settings link
		return $links;
	}
}
```

## Rules

- Use Hooker for feature hooks (enqueue, admin, frontend, REST, AJAX)
- Call feature `init()` from provider `boot()` so hooks queue before `hooker->run()`
- Support class methods (`$this`), closures, and invokable objects as callbacks
- Pass `$this` as second parameter for class method hooks
- Use method name as string (third parameter) when it differs from the hook name
- Use dependency injection to get Hooker when possible
- Register hook targets in the feature class `init()` method
- Use `add_actions()` / `add_filters()` for multiple hooks
- Avoid deferring Hooker registration to a WordPress `init` action unless hooks register with raw `add_action` (discouraged)

