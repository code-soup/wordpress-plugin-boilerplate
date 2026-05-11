---
name: service-provider
description: Create service providers to register and boot services in the WordPress plugin. Use when adding new features, controllers, or services that need dependency injection and initialization.
---

# Service Provider

Service providers register and boot services. Use for all new features.

## Create Service Provider

### Method 1: Extend AbstractServiceProvider

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
		parent::boot();

		$settings = $this->container->get( 'admin.settings' );
		$settings->init();
	}
}
```

### Method 2: Implement Interface

```php
<?php

namespace WPPB\Providers;

use WPPB\Contracts\ServiceProviderInterface;
use WPPB\Core\Container;
use WPPB\Admin\Dashboard;

defined( 'ABSPATH' ) || exit;

class DashboardServiceProvider implements ServiceProviderInterface {

	private Container $container;

	public function __construct( Container $container ) {
		$this->container = $container;
	}

	public function register(): void {
		$this->container->singleton(
			'admin.dashboard',
			Dashboard::class
		);
	}

	public function boot(): void {
		$dashboard = $this->container->get( 'admin.dashboard' );
		$dashboard->init();
	}
}
```

## Register Service Provider

Edit `includes/core/class-plugin.php`:

```php
protected array $providers = array(
	AdminServiceProvider::class,
	FrontendServiceProvider::class,
	SettingsServiceProvider::class, // Add here
);
```

## Binding Types

### Singleton

One instance shared across application:

```php
$this->singleton( 'service.name', ServiceClass::class );
```

### Bind

New instance each time:

```php
$this->bind( 'service.name', ServiceClass::class );
```

### Instance

Register existing instance:

```php
$this->instance( 'service.name', $instance );
```

## Multiple Services

```php
public function register(): void {
	$this->singleton( 'admin.settings', SettingsPage::class );
	$this->singleton( 'admin.dashboard', Dashboard::class );
	$this->singleton( 'admin.notices', NoticeManager::class );
}

public function boot(): void {
	parent::boot();

	$this->container->get( 'admin.settings' )->init();
	$this->container->get( 'admin.dashboard' )->init();
	$this->container->get( 'admin.notices' )->init();
}
```

## Conditional Registration

```php
public function register(): void {
	if ( is_admin() ) {
		$this->singleton( 'admin.settings', SettingsPage::class );
	}

	if ( wp_doing_ajax() ) {
		$this->singleton( 'ajax.handler', AjaxHandler::class );
	}
}
```

## Complete Example

```php
<?php

namespace WPPB\Providers;

use WPPB\Abstracts\AbstractServiceProvider;
use WPPB\Api\PostsController;
use WPPB\Services\PostService;

defined( 'ABSPATH' ) || exit;

class ApiServiceProvider extends AbstractServiceProvider {

	public function register(): void {
		// Register service
		$this->singleton(
			'services.posts',
			PostService::class
		);

		// Register controller
		$this->singleton(
			'api.posts',
			PostsController::class
		);
	}

	public function boot(): void {
		parent::boot();

		// Boot controller
		$controller = $this->container->get( 'api.posts' );
		$controller->init();
	}
}
```

## Rules

- Create one provider per feature/domain
- Use `register()` for binding services
- Use `boot()` for initialization
- **Always call `parent::boot()` first in boot() method**
- Add provider to `$providers` array in Plugin class
- Use singleton for shared instances
- Use descriptive service names (e.g., `admin.settings`, `api.posts`)
- Always call `init()` method in `boot()`

