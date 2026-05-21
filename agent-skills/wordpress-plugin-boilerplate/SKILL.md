---
name: wordpress-plugin-boilerplate
description: Understand and use the WordPress Plugin Boilerplate architecture - service providers, dependency injection, Hooker service, file naming conventions, and coding standards. Use when creating new features or understanding plugin structure.
---

# WordPress Plugin Boilerplate

Modern WordPress plugin architecture with dependency injection, service providers, and modular webpack.

## Architecture Overview

- **Container**: Dependency injection container
- **Service Providers**: Register and boot services
- **Hooker**: Manages WordPress hooks
- **Modular Webpack**: Build system in `src/config/`

## Directory Structure

```
includes/
├── core/              # Core classes (Plugin, Container, Hooker)
├── providers/         # Service providers
├── admin/             # Admin functionality
├── frontend/          # Public-facing functionality
├── api/               # REST API controllers
├── abstracts/         # Abstract classes
├── contracts/         # Interfaces
└── traits/            # Reusable traits

src/
├── config/            # Webpack configuration
├── scripts/           # JavaScript files
├── styles/            # SCSS files
├── icons/             # SVG icons
└── fonts/             # Font files
```

## File Naming Conventions

### PHP Files

- Classes: `class-{name}.php` (e.g., `class-settings-page.php`)
- Interfaces: `interface-{name}.php` (e.g., `interface-service-provider.php`)
- Traits: `trait-{name}.php` (e.g., `trait-helpers.php`)
- Functions: `functions-{name}.php` (e.g., `functions-helpers.php`)

### Class Names

- PascalCase: `SettingsPage`, `PostController`
- Match filename: `class-settings-page.php` → `SettingsPage`

### Namespaces

```php
namespace WPPB\Admin;        // Admin classes
namespace WPPB\Frontend;     // Frontend classes
namespace WPPB\Api;          // API controllers
namespace WPPB\Services;     // Services
namespace WPPB\Providers;    // Service providers
namespace WPPB\Repositories; // Repositories
```

## Coding Standards

### Array Syntax

Always use `array()`, never `[]`:

```php
// Correct
$items = array( 'one', 'two', 'three' );

// Wrong
$items = ['one', 'two', 'three'];
```

### Hooks

Never use `add_action`/`add_filter` directly. Always use Hooker:

```php
// Correct
$this->hooker->add_action( 'init', $this, 'init_method' );

// Wrong
add_action( 'init', array( $this, 'init_method' ) );
```

### Dependency Injection

Use constructor injection:

```php
public function __construct(
	Hooker $hooker,
	array $config
) {
	$this->hooker = $hooker;
	$this->config = $config;
}
```

### Security

Always escape output and sanitize input:

```php
echo esc_html( $text );
echo esc_url( $url );
echo esc_attr( $attribute );
echo wp_kses_post( $html );

$value = sanitize_text_field( $_POST['field'] );
$email = sanitize_email( $_POST['email'] );
```

### Translations

Make all strings translatable:

```php
__( 'Text', '__PLUGIN_TEXTDOMAIN__' );
esc_html__( 'Text', '__PLUGIN_TEXTDOMAIN__' );
esc_attr__( 'Text', '__PLUGIN_TEXTDOMAIN__' );

sprintf(
	__( 'Hello %s', '__PLUGIN_TEXTDOMAIN__' ),
	$name
);
```

## Adding New Feature

### Step 1: Create Class

**File**: `includes/admin/class-settings-page.php`

```php
<?php

namespace WPPB\Admin;

use WPPB\Core\Hooker;

defined( 'ABSPATH' ) || exit;

class SettingsPage {

	private Hooker $hooker;
	private array $config;

	public function __construct( Hooker $hooker, array $config ) {
		$this->hooker = $hooker;
		$this->config = $config;
	}

	public function init(): void {
		$this->hooker->add_action(
			'admin_menu',
			$this,
			'add_menu_page'
		);
	}

	public function add_menu_page(): void {
		add_menu_page(
			__( 'Settings', '__PLUGIN_TEXTDOMAIN__' ),
			__( 'Settings', '__PLUGIN_TEXTDOMAIN__' ),
			'manage_options',
			'wppb-settings',
			array( $this, 'render' )
		);
	}

	public function render(): void {
		// Render settings page
	}
}
```

### Step 2: Create Service Provider

**File**: `includes/providers/class-settings-service-provider.php`

```php
<?php

namespace WPPB\Providers;

use WPPB\Abstracts\AbstractServiceProvider;
use WPPB\Admin\SettingsPage;

defined( 'ABSPATH' ) || exit;

class SettingsServiceProvider extends AbstractServiceProvider {

	public function register(): void {
		$this->singleton(
			'admin.settings',
			SettingsPage::class
		);
	}

	public function boot(): void {
		if ( is_admin() ) {
			$this->container->get( 'admin.settings' )->init();
		}
	}
}
```

### Step 3: Register Provider

**File**: `includes/core/class-plugin.php`

```php
protected array $providers = array(
	AdminServiceProvider::class,
	FrontendServiceProvider::class,
	SettingsServiceProvider::class, // Add here
);
```

## Common Patterns

### Admin Page

1. Create class in `includes/admin/`
2. Use Hooker for `admin_menu` hook
3. Register in service provider
4. Add provider to Plugin class

### REST API Endpoint

1. Create controller in `includes/api/`
2. Use Hooker for `rest_api_init` hook
3. Register routes in controller
4. Register in service provider

### Custom Post Type

1. Create class in `includes/post-types/`
2. Use Hooker for `init` hook
3. Register post type in method
4. Register in service provider

### AJAX Handler

1. Create class in `includes/ajax/`
2. Use Hooker for `wp_ajax_*` hooks
3. Handle request and return JSON
4. Register in service provider

## Rules

- Use file naming convention: `class-{name}.php`
- Use `array()` syntax, never `[]`
- Use Hooker service, never direct `add_action`/`add_filter`
- Use dependency injection
- Create service provider for each feature
- Register provider in Plugin class
- Always escape output
- Always sanitize input
- Make strings translatable
- Use `defined( 'ABSPATH' ) || exit;` in all files

