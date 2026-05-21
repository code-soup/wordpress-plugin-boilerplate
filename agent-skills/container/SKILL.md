---
name: container
description: Use dependency injection container to resolve services and manage dependencies. Use when retrieving services, implementing dependency injection, or managing class instances.
---

# Container Service

The Container manages dependency injection. Use for service resolution and dependency management.

## Get Container

### From Global Plugin Instance

```php
use function WPPB\plugin;

$container = plugin()->get_container();
```

### In Service Provider

```php
$this->container->get( 'service.name' );
```

## Retrieve Service

```php
$service = $container->get( 'admin.settings' );
```

## Register Service

### Singleton

```php
$container->singleton( 'service.name', ServiceClass::class );

// With closure
$container->singleton( 'service.name', function( $container ) {
	return new ServiceClass(
		$container->get( 'dependency' )
	);
} );
```

### Bind

```php
$container->bind( 'service.name', ServiceClass::class );

// With closure
$container->bind( 'service.name', function( $container ) {
	return new ServiceClass();
} );
```

### Instance

```php
$instance = new ServiceClass();
$container->instance( 'service.name', $instance );
```

## Check if Service Exists

```php
if ( $container->has( 'service.name' ) ) {
	$service = $container->get( 'service.name' );
}
```

## Automatic Dependency Resolution

Container automatically resolves constructor dependencies:

```php
class SettingsPage {
	public function __construct(
		Hooker $hooker,
		array $config
	) {
		$this->hooker = $hooker;
		$this->config = $config;
	}
}

// Container resolves Hooker automatically
$container->singleton( 'admin.settings', SettingsPage::class );
$settings = $container->get( 'admin.settings' );
```

## Manual Dependency Injection

```php
$container->singleton( 'service.name', function( $container ) {
	return new ServiceClass(
		$container->get( 'hooker' ),
		$container->get( 'config' ),
		$container->get( 'custom.dependency' )
	);
} );
```

## Built-in Services

Available services:

- `hooker` - Hooker service
- `config` - Plugin configuration array
- `admin.enqueue` - Admin asset enqueuer
- `frontend.enqueue` - Frontend asset enqueuer

## Complete Example

```php
<?php

namespace WPPB\Services;

use WPPB\Core\Hooker;
use WPPB\Repositories\PostRepository;

class PostService {

	private Hooker $hooker;
	private PostRepository $repository;
	private array $config;

	public function __construct(
		Hooker $hooker,
		PostRepository $repository,
		array $config
	) {
		$this->hooker     = $hooker;
		$this->repository = $repository;
		$this->config     = $config;
	}

	public function init(): void {
		$this->hooker->add_action(
			'init',
			$this,
			'register_post_type'
		);
	}

	public function register_post_type(): void {
		// Register custom post type
	}
}
```

Register in service provider:

```php
public function register(): void {
	// Register repository
	$this->singleton(
		'repositories.post',
		PostRepository::class
	);

	// Register service (dependencies auto-resolved)
	$this->singleton(
		'services.post',
		PostService::class
	);
}

public function boot(): void {
	$this->container->get( 'services.post' )->init();
}
```

## Rules

- Use dependency injection in constructors
- Register services in service providers
- Use singleton for shared instances
- Use bind for new instances each time
- Let container auto-resolve dependencies when possible
- Use descriptive service names
- Never instantiate services with `new` directly
- Always retrieve services from container

