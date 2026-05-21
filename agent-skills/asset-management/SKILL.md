---
name: asset-management
description: Enqueue scripts and styles using the Assets service. Use when adding JavaScript or CSS files to admin or frontend, localizing scripts, or managing asset dependencies.
---

# Asset Management

Use the Assets service to enqueue scripts and styles. Never use wp_enqueue_script/wp_enqueue_style directly.

## Get Assets Service

```php
use WPPB\Core\Assets;

public function __construct( Assets $assets ) {
	$this->assets = $assets;
}
```

## Enqueue Script

```php
$this->assets->enqueue_script(
	'handle',
	'script-name.js',
	array( 'jquery' ),
	true
);
```

Parameters:
- Handle (unique identifier)
- Filename (in dist/scripts/)
- Dependencies array
- In footer (true/false)

## Enqueue Style

```php
$this->assets->enqueue_style(
	'handle',
	'style-name.css',
	array(),
	'all'
);
```

Parameters:
- Handle (unique identifier)
- Filename (in dist/styles/)
- Dependencies array
- Media type ('all', 'screen', 'print')

## Localize Script

Pass data from PHP to JavaScript:

```php
$this->assets->localize_script(
	'handle',
	'objectName',
	array(
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'my_nonce' ),
		'strings' => array(
			'success' => __( 'Success!', '__PLUGIN_TEXTDOMAIN__' ),
			'error'   => __( 'Error!', '__PLUGIN_TEXTDOMAIN__' ),
		),
	)
);
```

Access in JavaScript:

```javascript
console.log( objectName.ajaxUrl );
console.log( objectName.nonce );
console.log( objectName.strings.success );
```

## Add Inline Script

```php
$this->assets->add_inline_script(
	'handle',
	'console.log("Inline script");',
	'after'
);
```

## Add Inline Style

```php
$this->assets->add_inline_style(
	'handle',
	'.custom-class { color: red; }'
);
```

## Admin Assets

```php
<?php

namespace WPPB\Admin;

use WPPB\Core\Assets;
use WPPB\Core\Hooker;

class AdminAssets {

	private Assets $assets;
	private Hooker $hooker;

	public function __construct( Assets $assets, Hooker $hooker ) {
		$this->assets = $assets;
		$this->hooker = $hooker;
	}

	public function init(): void {
		$this->hooker->add_action(
			'admin_enqueue_scripts',
			$this,
			'enqueue_assets'
		);
	}

	public function enqueue_assets(): void {
		$this->assets->enqueue_style(
			'wppb-admin',
			'admin-common.css'
		);

		$this->assets->enqueue_script(
			'wppb-admin',
			'admin-common.js',
			array( 'jquery' ),
			true
		);

		$this->assets->localize_script(
			'wppb-admin',
			'wppbAdmin',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'wppb_admin' ),
			)
		);
	}
}
```

## Frontend Assets

```php
<?php

namespace WPPB\Frontend;

use WPPB\Core\Assets;
use WPPB\Core\Hooker;

class FrontendAssets {

	private Assets $assets;
	private Hooker $hooker;

	public function __construct( Assets $assets, Hooker $hooker ) {
		$this->assets = $assets;
		$this->hooker = $hooker;
	}

	public function init(): void {
		$this->hooker->add_action(
			'wp_enqueue_scripts',
			$this,
			'enqueue_assets'
		);
	}

	public function enqueue_assets(): void {
		$this->assets->enqueue_style(
			'wppb-main',
			'main-common.css'
		);

		$this->assets->enqueue_script(
			'wppb-main',
			'main-common.js',
			array(),
			true
		);
	}
}
```

## Conditional Loading

```php
public function enqueue_assets(): void {
	// Only on specific page
	if ( is_page( 'contact' ) ) {
		$this->assets->enqueue_script( 'contact-form', 'contact.js' );
	}

	// Only for logged-in users
	if ( is_user_logged_in() ) {
		$this->assets->enqueue_script( 'user-dashboard', 'dashboard.js' );
	}

	// Only on admin settings page
	$screen = get_current_screen();
	if ( $screen && 'settings_page_wppb' === $screen->id ) {
		$this->assets->enqueue_script( 'settings', 'settings.js' );
	}
}
```

## Rules

- Use Assets service, never wp_enqueue_* directly
- Enqueue admin assets on `admin_enqueue_scripts` hook
- Enqueue frontend assets on `wp_enqueue_scripts` hook
- Always specify dependencies
- Use localize_script for passing PHP data to JS
- Files must exist in dist/scripts/ or dist/styles/
- Use unique handles
- Load scripts in footer when possible (true parameter)

