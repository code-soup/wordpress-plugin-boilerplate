# Service Providers

Service Providers are the primary way to organize and register functionality in this WordPress plugin boilerplate. They follow a dependency injection pattern and provide a clean, modular approach to structuring your plugin's features.

## Overview

Service Providers serve two main purposes:
1. **Register services** into the dependency injection container during the `register()` phase
2. **Boot/initialize services** and add WordPress hooks during the `boot()` phase

## Two Approaches

You can create service providers in two ways:
1. **With Interface** - Extending `AbstractServiceProvider` (recommended)
2. **Without Interface** - Implementing `ServiceProviderInterface` directly

---

## Approach 1: Using AbstractServiceProvider (Recommended)

This is the recommended approach as it provides helpful methods and follows the established patterns.

### Step 1: Create Your Feature Class

First, create the class that contains your actual functionality:

```php
<?php
/**
 * Email Service.
 *
 * @package WPPB
 */

declare(strict_types=1);

namespace WPPB\Services;

use WPPB\Core\Hooker;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Handles email functionality for the plugin.
 */
class EmailService {

	/**
	 * The hooker service.
	 *
	 * @var Hooker
	 */
	private Hooker $hooker;

	/**
	 * Constructor.
	 *
	 * @param Hooker $hooker The hooker service for registering WordPress hooks.
	 */
	public function __construct( Hooker $hooker ) {
		$this->hooker = $hooker;
	}

	/**
	 * Initialize the email service.
	 */
	public function init(): void {
		$this->hooker->add_action( 'wp_mail_failed', $this, 'log_email_failure' );
		$this->hooker->add_filter( 'wp_mail_from', $this, 'set_from_email' );
	}

	/**
	 * Log email failures.
	 *
	 * @param \WP_Error $error The error object.
	 */
	public function log_email_failure( \WP_Error $error ): void {
		error_log( 'Email failed: ' . $error->get_error_message() );
	}

	/**
	 * Set the from email address.
	 *
	 * @param string $email The current from email.
	 * @return string The modified from email.
	 */
	public function set_from_email( string $email ): string {
		return 'noreply@' . wp_parse_url( home_url(), PHP_URL_HOST );
	}

	/**
	 * Send a notification email.
	 *
	 * @param string $to      Recipient email address.
	 * @param string $subject Email subject.
	 * @param string $message Email message.
	 * @return bool Whether the email was sent successfully.
	 */
	public function send_notification( string $to, string $subject, string $message ): bool {
		return wp_mail( $to, $subject, $message );
	}
}
```

### Step 2: Create the Service Provider

Create a service provider that extends `AbstractServiceProvider`:

```php
<?php
/**
 * Email Service Provider.
 *
 * @package WPPB
 */

declare(strict_types=1);

namespace WPPB\Providers;

use WPPB\Abstracts\AbstractServiceProvider;
use WPPB\Services\EmailService;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Service provider for email functionality.
 */
class EmailServiceProvider extends AbstractServiceProvider {

	/**
	 * Register the service provider.
	 */
	public function register(): void {
		// Register the EmailService as a singleton
		$this->singleton( 'email', EmailService::class );
		
		// You can also register with a closure for more control
		$this->singleton( EmailService::class, function( $container ) {
			return new EmailService( $container->get( 'hooker' ) );
		});
	}

	/**
	 * Boot the service provider.
	 */
	public function boot(): void {
		// Initialize the email service
		$email_service = $this->container->get( 'email' );
		$email_service->init();
	}
}
```

### Step 3: Register the Service Provider

Add your service provider to the `$providers` array in the main plugin class:

```php
// In includes/core/Plugin.php, add to the $providers array:
protected array $providers = array(
	AdminServiceProvider::class,
	FrontendServiceProvider::class,
	\WPPB\Providers\EmailServiceProvider::class, // Add your provider here
);
```

---

## Approach 2: Without AbstractServiceProvider

If you prefer to implement the interface directly, you can do so:

### Step 1: Create Your Service Provider

```php
<?php
/**
 * Custom Service Provider.
 *
 * @package WPPB
 */

declare(strict_types=1);

namespace WPPB\Providers;

use WPPB\Core\Container;
use WPPB\Interfaces\ServiceProviderInterface;
use WPPB\Services\CustomService;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Custom service provider implementing the interface directly.
 */
class CustomServiceProvider implements ServiceProviderInterface {

	/**
	 * The container instance.
	 *
	 * @var Container
	 */
	private Container $container;

	/**
	 * Constructor.
	 *
	 * @param Container $container The dependency injection container.
	 */
	public function __construct( Container $container ) {
		$this->container = $container;
	}

	/**
	 * Register the service provider.
	 */
	public function register(): void {
		$this->container->singleton( 'custom', CustomService::class );
	}

	/**
	 * Get the container.
	 *
	 * @return Container
	 */
	public function get_container(): Container {
		return $this->container;
	}

	/**
	 * Boot the service provider (optional - not required by interface).
	 */
	public function boot(): void {
		$custom_service = $this->container->get( 'custom' );
		$custom_service->init();
	}
}
```

---

## Advanced Examples

### Multiple Services in One Provider

```php
<?php
/**
 * Integration Service Provider.
 *
 * @package WPPB
 */

declare(strict_types=1);

namespace WPPB\Providers;

use WPPB\Abstracts\AbstractServiceProvider;
use WPPB\Integrations\WooCommerceIntegration;
use WPPB\Integrations\MailChimpIntegration;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Handles third-party integrations.
 */
class IntegrationServiceProvider extends AbstractServiceProvider {

	/**
	 * Register the service provider.
	 */
	public function register(): void {
		// Register multiple related services
		$this->singleton( 'woocommerce_integration', WooCommerceIntegration::class );
		$this->singleton( 'mailchimp_integration', MailChimpIntegration::class );
		
		// Create aliases for easier access
		$this->alias( 'woocommerce_integration', 'woo' );
		$this->alias( 'mailchimp_integration', 'mailchimp' );
	}

	/**
	 * Boot the service provider.
	 */
	public function boot(): void {
		// Only initialize if WooCommerce is active
		if ( class_exists( 'WooCommerce' ) ) {
			$this->container->get( 'woo' )->init();
		}

		// Only initialize if MailChimp API key is configured
		if ( defined( 'MAILCHIMP_API_KEY' ) ) {
			$this->container->get( 'mailchimp' )->init();
		}
	}
}
```

### Conditional Service Registration

```php
<?php
/**
 * Admin Enhancement Service Provider.
 *
 * @package WPPB
 */

declare(strict_types=1);

namespace WPPB\Providers;

use WPPB\Abstracts\AbstractServiceProvider;
use WPPB\Admin\DashboardWidgets;
use WPPB\Admin\CustomColumns;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Provides admin-specific enhancements.
 */
class AdminEnhancementServiceProvider extends AbstractServiceProvider {

	/**
	 * Register the service provider.
	 */
	public function register(): void {
		// Only register admin services if we're in admin
		if ( is_admin() ) {
			$this->singleton( 'dashboard_widgets', DashboardWidgets::class );
			$this->singleton( 'custom_columns', CustomColumns::class );
		}
	}

	/**
	 * Boot the service provider.
	 */
	public function boot(): void {
		// Only boot if we're in admin and user has appropriate capabilities
		if ( is_admin() && current_user_can( 'manage_options' ) ) {
			$this->container->get( 'dashboard_widgets' )->init();
			$this->container->get( 'custom_columns' )->init();
		}
	}
}
```

---

## Best Practices

### 1. Use Descriptive Names
```php
// Good
$this->singleton( 'user_notification_service', UserNotificationService::class );

// Avoid
$this->singleton( 'uns', UserNotificationService::class );
```

### 2. Leverage Dependency Injection
```php
// In your service class constructor
public function __construct( 
	Hooker $hooker, 
	Assets $assets, 
	DatabaseService $database 
) {
	$this->hooker = $hooker;
	$this->assets = $assets;
	$this->database = $database;
}
```

### 3. Use Conditional Loading
```php
public function boot(): void {
	// Only load if specific conditions are met
	if ( $this->should_load_feature() ) {
		$this->container->get( 'feature_service' )->init();
	}
}

private function should_load_feature(): bool {
	return is_admin() && current_user_can( 'manage_options' );
}
```

### 4. Organize by Feature, Not by Type
```php
// Good - organized by feature
- EmailServiceProvider
- IntegrationServiceProvider
- ReportingServiceProvider

// Avoid - organized by type
- ModelServiceProvider
- ControllerServiceProvider
- ViewServiceProvider
```

### 5. Keep Services Focused
Each service should have a single responsibility. If your service is doing too many things, consider splitting it into multiple services.

---

## Accessing Services

Once registered, you can access your services from anywhere in your plugin:

### From Another Service Provider
```php
public function boot(): void {
	$email_service = $this->container->get( 'email' );
	$email_service->send_notification( 'admin@site.com', 'Test', 'Hello World' );
}
```

### From Plugin Functions
```php
function send_welcome_email( $user_email ) {
	$email_service = \WPPB\plugin()->get( 'email' );
	return $email_service->send_notification( 
		$user_email, 
		'Welcome!', 
		'Thanks for joining us!' 
	);
}
```

### From WordPress Hooks
```php
add_action( 'user_register', function( $user_id ) {
	$user = get_user_by( 'id', $user_id );
	$email_service = \WPPB\plugin()->get( 'email' );
	$email_service->send_notification( 
		$user->user_email, 
		'Welcome!', 
		'Thanks for registering!' 
	);
});
```

---

## Service Provider Lifecycle

1. **Construction**: Service provider is instantiated with the container
2. **Registration**: `register()` method is called to bind services into container
3. **Booting**: `boot()` method is called after all providers are registered
4. **Hook Execution**: All registered WordPress hooks are executed via `Hooker->run()`

This lifecycle ensures that all services are available when you need them during the boot phase. 