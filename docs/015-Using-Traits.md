# Using Traits

Traits are a mechanism for code reuse in single-inheritance languages like PHP. In this boilerplate, they provide reusable methods that can be easily added to different classes without inheritance.

## Why Use Traits?

Traits help you:
- **Avoid code duplication**: Share common functionality across multiple classes
- **Maintain clean code**: Keep related methods grouped together
- **Improve organization**: Better than large procedural helper files
- **Enable composition**: Add multiple traits to a single class

---

## Available Traits

### 1. LoggingTrait

Contains methods for logging debug information to the WordPress debug log.

**Location**: `includes/traits/trait-logging.php`

**Methods**:
- `log( string $message, string $level = 'info' )`: Log a message

**Requirements**: WordPress debug logging must be enabled (`WP_DEBUG_LOG` constant).

**Example**:
```php
<?php

namespace WPPB\Services;

use WPPB\Traits\LoggingTrait;

class EmailService {

	use LoggingTrait;

	public function send_email( string $to, string $subject, string $message ): bool {
		$this->log(
			sprintf(
				'Sending email to %s with subject: %s',
				$to,
				$subject
			)
		);

		$result = wp_mail( $to, $subject, $message );

		if ( ! $result ) {
			$this->log(
				sprintf(
					'Failed to send email to %s',
					$to
				),
				'error'
			);
		}

		return $result;
	}
}
```

---

### 2. RequirementChecksTrait

Helper methods for checking WordPress and PHP version requirements.

**Location**: `includes/traits/trait-requirement-checks.php`

**Methods**:
- `run_requirement_checks( array $config )`: Run all requirement checks (throws exception on failure)
- `is_wp_version_ok( string $min_wp_version )`: Check WordPress version
- `is_php_version_ok( string $min_php_version )`: Check PHP version

**Example**:
```php
<?php

namespace WPPB\Core;

use WPPB\Traits\RequirementChecksTrait;

class Activator {

	use RequirementChecksTrait;

	public static function activate(): void {
		$config = array(
			'MIN_WP_VERSION'     => '5.8',
			'MIN_PHP_VERSION'    => '7.4',
		);

		// Throws \Exception if requirements not met
		// WordPress handles deactivation automatically on activation failure
		self::run_requirement_checks( $config );
	}
}
```

---

## Creating Custom Traits

You can create your own traits to group functionality for specific purposes.

### Step 1: Create the Trait File

Create a new file in `includes/traits/` following the naming convention `trait-{name}.php`.

**File**: `includes/traits/trait-caching.php`

```php
<?php
/**
 * Caching trait.
 *
 * @package WPPB
 */

declare(strict_types=1);

namespace WPPB\Traits;

defined( 'ABSPATH' ) || exit;

/**
 * The CachingTrait trait.
 */
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

### Step 2: Use the Trait

```php
<?php

namespace WPPB\Services;

use WPPB\Traits\CachingTrait;

class DataService {

	use CachingTrait;

	public function get_data(): array {
		$cached = $this->get_cache( 'my_data' );

		if ( false !== $cached ) {
			return $cached;
		}

		$data = $this->fetch_data_from_api();

		$this->set_cache( 'my_data', $data, HOUR_IN_SECONDS );

		return $data;
	}

	private function fetch_data_from_api(): array {
		// API call logic
		return array();
	}
}
```

---

## Using Multiple Traits

You can use multiple traits in a single class.

```php
<?php

namespace WPPB\Services;

use WPPB\Traits\LoggingTrait;
use WPPB\Traits\CachingTrait;

class ComplexService {

	use LoggingTrait;
	use CachingTrait;

	public function process_data(): array {
		$this->log( 'Processing data' );

		$cached = $this->get_cache( 'processed_data' );

		if ( false !== $cached ) {
			$this->log( 'Returning cached data' );
			return $cached;
		}

		$data = $this->perform_processing();

		$this->set_cache( 'processed_data', $data );

		return $data;
	}

	private function perform_processing(): array {
		// Processing logic
		return array();
	}
}
```

---

## Trait Conflict Resolution

If two traits have methods with the same name, you must resolve the conflict.

```php
<?php

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
		echo $this->process();   // Uses TraitA::process
		echo $this->processB();  // Uses TraitB::process
	}
}
```

---

## Best Practices

1. **Keep Traits Focused**: Each trait should have a single, clear purpose
2. **Avoid State**: Traits should not maintain complex state
3. **Document Dependencies**: Clearly document any property or method requirements
4. **Use Descriptive Names**: Name traits based on their functionality (e.g., `CachingTrait`, `LoggingTrait`)
5. **Prefer Composition**: Use traits for horizontal code reuse, not as a replacement for proper class hierarchies
