---
name: traits
description: Use existing traits and create custom traits for code reuse. Use when sharing common functionality across multiple classes without inheritance.
---

# Traits

Traits provide reusable methods across classes.

## Available Traits

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

### RequirementChecksTrait

Check WordPress and PHP version requirements. Throws exception on failure.

**Location**: `includes/traits/trait-requirement-checks.php`

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
		// WordPress auto-deactivates on activation failure
		self::run_requirement_checks( $config );
	}
}
```

## Use Multiple Traits

```php
<?php

namespace WPPB\Services;

use WPPB\Traits\LoggingTrait;
use WPPB\Traits\CachingTrait;

class DataService {
	use LoggingTrait;
	use CachingTrait;

	public function process(): void {
		$this->log( 'Processing data' );

		$cached = $this->get_cache( 'data' );
		if ( $cached ) {
			return $cached;
		}

		// Process and cache
		$data = array();
		$this->set_cache( 'data', $data );
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

