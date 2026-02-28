---
name: traits
description: Use existing traits and create custom traits for code reuse. Use when sharing common functionality across multiple classes without inheritance.
---

# Traits

Traits provide reusable methods across classes.

## Available Traits

### HelpersTrait

Access plugin configuration values.

**Location**: `includes/traits/trait-helpers.php`

**Requires**: Class must have `$config` property.

```php
use WPPB\Traits\HelpersTrait;

class MyClass {
	use HelpersTrait;

	protected array $config;

	public function __construct( array $config ) {
		$this->config = $config;
	}

	public function example(): void {
		$name    = $this->get_name();
		$version = $this->get_version();
		$prefix  = $this->get_prefix();
		$url     = $this->get_url();
		$path    = $this->get_base_path();
		$id      = $this->get_plugin_id( '-suffix' );
	}
}
```

### LoggingTrait

Log debug messages to WordPress debug log.

**Location**: `includes/traits/trait-logging.php`

**Requires**: `WP_DEBUG_LOG` enabled.

```php
use WPPB\Traits\LoggingTrait;

class MyClass {
	use LoggingTrait;

	public function process(): void {
		$this->log( 'Processing started', 'info' );
		$this->log( 'Error occurred', 'error' );
		$this->log( 'Warning message', 'warning' );
	}
}
```

### ValidationTrait

Validate data using Respect\Validation library.

**Location**: `includes/traits/trait-validation.php`

```php
use WPPB\Traits\ValidationTrait;

class MyClass {
	use ValidationTrait;

	public function validate_email( string $email ): bool {
		return $this->validate( $email, 'email' );
	}

	public function validate_url( string $url ): bool {
		return $this->validate( $url, 'url' );
	}
}
```

### RequirementChecksTrait

Check WordPress and PHP version requirements.

**Location**: `includes/traits/trait-requirement-checks.php`

```php
use WPPB\Traits\RequirementChecksTrait;

class Activator {
	use RequirementChecksTrait;

	public static function activate(): void {
		$config = array(
			'MIN_WP_VERSION'  => '5.8',
			'MIN_PHP_VERSION' => '7.4',
			'PLUGIN_BASENAME' => 'plugin/plugin.php',
		);

		self::run_requirement_checks( $config );
	}
}
```

## Use Multiple Traits

```php
<?php

namespace WPPB\Services;

use WPPB\Traits\HelpersTrait;
use WPPB\Traits\LoggingTrait;

class DataService {
	use HelpersTrait;
	use LoggingTrait;

	protected array $config;

	public function __construct( array $config ) {
		$this->config = $config;
	}

	public function process(): void {
		$this->log(
			sprintf(
				'Processing for plugin: %s',
				$this->get_name()
			)
		);
	}
}
```

## Create Custom Trait

**File**: `includes/traits/trait-caching.php`

```php
<?php

namespace WPPB\Traits;

defined( 'ABSPATH' ) || exit;

trait CachingTrait {

	protected function get_cache( string $key ) {
		return get_transient( $this->get_cache_key( $key ) );
	}

	protected function set_cache( string $key, $value, int $expiration = 3600 ): bool {
		return set_transient(
			$this->get_cache_key( $key ),
			$value,
			$expiration
		);
	}

	protected function delete_cache( string $key ): bool {
		return delete_transient( $this->get_cache_key( $key ) );
	}

	protected function get_cache_key( string $key ): string {
		return sprintf(
			'wppb_%s',
			sanitize_key( $key )
		);
	}
}
```

Use custom trait:

```php
<?php

namespace WPPB\Services;

use WPPB\Traits\CachingTrait;

class ApiService {
	use CachingTrait;

	public function get_data(): array {
		$cached = $this->get_cache( 'api_data' );

		if ( false !== $cached ) {
			return $cached;
		}

		$data = $this->fetch_from_api();

		$this->set_cache( 'api_data', $data, HOUR_IN_SECONDS );

		return $data;
	}

	private function fetch_from_api(): array {
		return array();
	}
}
```

## Trait Conflict Resolution

When two traits have same method name:

```php
trait TraitA {
	public function process() {
		return 'A';
	}
}

trait TraitB {
	public function process() {
		return 'B';
	}
}

class MyClass {
	use TraitA, TraitB {
		TraitA::process insteadof TraitB;
		TraitB::process as processB;
	}

	public function run() {
		$this->process();   // Uses TraitA
		$this->processB();  // Uses TraitB
	}
}
```

## Rules

- Create traits in `includes/traits/`
- Use naming: `trait-{name}.php`
- Trait name: `{Name}Trait`
- Document required properties/methods
- Keep traits focused on single purpose
- Use protected methods in traits
- Don't maintain complex state in traits
- Use for horizontal code reuse

