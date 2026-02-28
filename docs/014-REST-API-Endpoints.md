# REST API Endpoints

This guide explains how to add custom REST API endpoints to your WordPress plugin using the boilerplate's architecture.

## Overview

REST API endpoints allow external applications or JavaScript to interact with your plugin's functionality. The boilerplate uses a controller-based approach with proper dependency injection and hook management.

## Creating a REST API Controller

### Step 1: Create the Controller Class

Create a new controller file in `includes/api/` following the naming convention `class-{endpoint}-controller.php`.

**File**: `includes/api/class-posts-controller.php`

```php
<?php
/**
 * Posts REST Controller.
 *
 * @package WPPB
 */

declare(strict_types=1);

namespace WPPB\Api;

use WPPB\Core\Hooker;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

defined( 'ABSPATH' ) || exit;

/**
 * REST API controller for posts.
 */
class PostsController {

	private Hooker $hooker;

	public function __construct( Hooker $hooker ) {
		$this->hooker = $hooker;
	}

	public function init(): void {
		$this->hooker->add_action(
			'rest_api_init',
			$this,
			'register_routes'
		);
	}

	public function register_routes(): void {
		register_rest_route(
			'wppb/v1',
			'/posts',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_items' ),
				'permission_callback' => array( $this, 'check_permission' ),
			)
		);

		register_rest_route(
			'wppb/v1',
			'/posts/(?P<id>\d+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_item' ),
				'permission_callback' => array( $this, 'check_permission' ),
				'args'                => array(
					'id' => array(
						'validate_callback' => function( $param ) {
							return is_numeric( $param );
						},
					),
				),
			)
		);

		register_rest_route(
			'wppb/v1',
			'/posts',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'create_item' ),
				'permission_callback' => array( $this, 'check_permission' ),
				'args'                => $this->get_create_args(),
			)
		);
	}

	public function check_permission(): bool {
		return current_user_can( 'manage_options' );
	}

	public function get_items( WP_REST_Request $request ): WP_REST_Response {
		$page     = $request->get_param( 'page' )
			? (int) $request->get_param( 'page' )
			: 1;
		$per_page = $request->get_param( 'per_page' )
			? (int) $request->get_param( 'per_page' )
			: 10;

		$items = array(); // Fetch your items here

		return new WP_REST_Response(
			array(
				'items'      => $items,
				'page'       => $page,
				'per_page'   => $per_page,
				'total'      => count( $items ),
			),
			200
		);
	}

	public function get_item( WP_REST_Request $request ) {
		$id   = (int) $request->get_param( 'id' );
		$item = array(); // Fetch your item here

		if ( empty( $item ) ) {
			return new WP_Error(
				'not_found',
				__( 'Item not found', '__PLUGIN_TEXTDOMAIN__' ),
				array( 'status' => 404 )
			);
		}

		return new WP_REST_Response( $item, 200 );
	}

	public function create_item( WP_REST_Request $request ) {
		$title   = $request->get_param( 'title' );
		$content = $request->get_param( 'content' );

		// Create your item here

		return new WP_REST_Response(
			array(
				'message' => __( 'Item created successfully', '__PLUGIN_TEXTDOMAIN__' ),
			),
			201
		);
	}

	private function get_create_args(): array {
		return array(
			'title'   => array(
				'required'          => true,
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'content' => array(
				'required'          => true,
				'type'              => 'string',
				'sanitize_callback' => 'wp_kses_post',
			),
		);
	}
}
```

---

## Step 2: Create a Service Provider

Create a service provider to register and initialize your controller.

**File**: `includes/providers/class-api-service-provider.php`

```php
<?php
/**
 * API Service Provider.
 *
 * @package WPPB
 */

declare(strict_types=1);

namespace WPPB\Providers;

use WPPB\Abstracts\AbstractServiceProvider;
use WPPB\Api\PostsController;

defined( 'ABSPATH' ) || exit;

/**
 * Service provider for REST API functionality.
 */
class ApiServiceProvider extends AbstractServiceProvider {

	public function register(): void {
		$this->singleton(
			'api.posts',
			PostsController::class
		);
	}

	public function boot(): void {
		$controller = $this->container->get( 'api.posts' );
		$controller->init();
	}
}
```

---

## Step 3: Register the Service Provider

Add your service provider to the `$providers` array in the main plugin class.

**File**: `includes/core/class-plugin.php`

```php
protected array $providers = array(
	AdminServiceProvider::class,
	FrontendServiceProvider::class,
	\WPPB\Providers\ApiServiceProvider::class, // Add this line
);
```

---

## Endpoint Structure

### Namespace and Version

All endpoints use the namespace `wppb/v1`. This creates URLs like:
- `https://yoursite.com/wp-json/wppb/v1/posts`
- `https://yoursite.com/wp-json/wppb/v1/posts/123`

### HTTP Methods

Use appropriate HTTP methods for different operations:
- `GET`: Retrieve data
- `POST`: Create new data
- `PUT` or `PATCH`: Update existing data
- `DELETE`: Remove data

---

## Permission Callbacks

Always implement permission callbacks to secure your endpoints.

### Public Endpoint
```php
public function check_permission(): bool {
	return true; // Anyone can access
}
```

### Admin Only
```php
public function check_permission(): bool {
	return current_user_can( 'manage_options' );
}
```

### Custom Capability
```php
public function check_permission(): bool {
	return current_user_can( 'edit_posts' );
}
```

### Logged-in Users
```php
public function check_permission(): bool {
	return is_user_logged_in();
}
```

---

## Request Validation

Use the `args` parameter to validate and sanitize input.

```php
register_rest_route(
	'wppb/v1',
	'/posts',
	array(
		'methods'             => 'POST',
		'callback'            => array( $this, 'create_item' ),
		'permission_callback' => array( $this, 'check_permission' ),
		'args'                => array(
			'title'   => array(
				'required'          => true,
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => function( $param ) {
					return ! empty( $param );
				},
			),
			'status'  => array(
				'required' => false,
				'type'     => 'string',
				'enum'     => array( 'draft', 'published' ),
				'default'  => 'draft',
			),
		),
	)
);
```

---

## Error Handling

Return proper error responses using `WP_Error`.

```php
public function get_item( WP_REST_Request $request ) {
	$id = (int) $request->get_param( 'id' );

	if ( $id <= 0 ) {
		return new WP_Error(
			'invalid_id',
			__( 'Invalid ID provided', '__PLUGIN_TEXTDOMAIN__' ),
			array( 'status' => 400 )
		);
	}

	$item = $this->fetch_item( $id );

	if ( ! $item ) {
		return new WP_Error(
			'not_found',
			__( 'Item not found', '__PLUGIN_TEXTDOMAIN__' ),
			array( 'status' => 404 )
		);
	}

	return new WP_REST_Response( $item, 200 );
}
```

---

## Testing Endpoints

### Using cURL

```bash
# GET request
curl -X GET https://yoursite.com/wp-json/wppb/v1/posts

# POST request
curl -X POST https://yoursite.com/wp-json/wppb/v1/posts \
  -H "Content-Type: application/json" \
  -d '{"title":"Test Post","content":"Test content"}'

# With authentication
curl -X GET https://yoursite.com/wp-json/wppb/v1/posts \
  --user username:password
```

### Using JavaScript

```javascript
// GET request
fetch( '/wp-json/wppb/v1/posts' )
	.then( response => response.json() )
	.then( data => console.log( data ) );

// POST request
fetch(
	'/wp-json/wppb/v1/posts',
	{
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
			'X-WP-Nonce': wpApiSettings.nonce
		},
		body: JSON.stringify(
			{
				title: 'Test Post',
				content: 'Test content'
			}
		)
	}
)
	.then( response => response.json() )
	.then( data => console.log( data ) );
```

---

## Best Practices

1. **Use Proper HTTP Status Codes**
   - `200`: Success
   - `201`: Created
   - `400`: Bad Request
   - `401`: Unauthorized
   - `403`: Forbidden
   - `404`: Not Found
   - `500`: Server Error

2. **Always Validate and Sanitize Input**
   - Use `sanitize_text_field()`, `sanitize_email()`, etc.
   - Implement validation callbacks

3. **Implement Proper Permission Checks**
   - Never skip permission callbacks
   - Use appropriate capabilities

4. **Return Consistent Response Formats**
   - Use consistent data structures
   - Include helpful error messages

5. **Version Your API**
   - Use versioned namespaces (`wppb/v1`, `wppb/v2`)
   - Maintain backward compatibility

