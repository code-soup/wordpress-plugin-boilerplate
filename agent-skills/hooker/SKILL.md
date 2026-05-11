---
name: hooker
description: Use Hooker service to register WordPress actions and filters. Use when adding WordPress hooks, filters, or actions in the plugin. Never use add_action or add_filter directly.
---

# Hooker Service

The Hooker service manages WordPress hooks. Always use Hooker, never direct `add_action`/`add_filter`.

## Get Hooker Instance

Use the `plugin()` helper function to get the Hooker service:

```php
use function WPPB\plugin;

$hooker = plugin()->get( 'hooker' );
```

**Important**: Do NOT call `plugin()` in class constructors during plugin initialization to avoid circular dependencies. Call it in `init()` methods instead.

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

- Always use Hooker service
- Never use `add_action()` or `add_filter()` directly
- Pass `$this` as second parameter
- Use method name as string (third parameter)
- Use dependency injection to get Hooker instance
- Register hooks in `init()` method
- Use `add_actions()` for multiple actions
- Use `add_filters()` for multiple filters
- Use `register()` for complex bulk registration

