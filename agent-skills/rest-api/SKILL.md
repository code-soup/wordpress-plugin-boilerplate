---
name: rest-api
description: Create REST API endpoints with controllers, routes, permissions, and validation. Use when adding custom REST API endpoints for external integrations or JavaScript applications.
---

# REST API

Create REST API controllers for custom endpoints.

## Create Controller

**File**: `includes/api/class-posts-controller.php`

```php
<?php

namespace WPPB\Api;

use WPPB\Core\Hooker;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

defined( 'ABSPATH' ) || exit;

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
		$items = array(); // Fetch items

		return new WP_REST_Response(
			array(
				'items' => $items,
				'total' => count( $items ),
			),
			200
		);
	}

	public function get_item( WP_REST_Request $request ) {
		$id   = (int) $request->get_param( 'id' );
		$item = array(); // Fetch item

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
		$title = $request->get_param( 'title' );

		// Create item

		return new WP_REST_Response(
			array(
				'message' => __( 'Created', '__PLUGIN_TEXTDOMAIN__' ),
			),
			201
		);
	}

	private function get_create_args(): array {
		return array(
			'title' => array(
				'required'          => true,
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
		);
	}
}
```

## Permission Callbacks

### Public

```php
public function check_permission(): bool {
	return true;
}
```

### Admin Only

```php
public function check_permission(): bool {
	return current_user_can( 'manage_options' );
}
```

### Logged In

```php
public function check_permission(): bool {
	return is_user_logged_in();
}
```

### Custom Capability

```php
public function check_permission(): bool {
	return current_user_can( 'edit_posts' );
}
```

## Request Validation

```php
'args' => array(
	'title' => array(
		'required'          => true,
		'type'              => 'string',
		'sanitize_callback' => 'sanitize_text_field',
		'validate_callback' => function( $param ) {
			return ! empty( $param );
		},
	),
	'status' => array(
		'required' => false,
		'type'     => 'string',
		'enum'     => array( 'draft', 'published' ),
		'default'  => 'draft',
	),
),
```

## Error Handling

```php
if ( $id <= 0 ) {
	return new WP_Error(
		'invalid_id',
		__( 'Invalid ID', '__PLUGIN_TEXTDOMAIN__' ),
		array( 'status' => 400 )
	);
}

if ( ! $item ) {
	return new WP_Error(
		'not_found',
		__( 'Not found', '__PLUGIN_TEXTDOMAIN__' ),
		array( 'status' => 404 )
	);
}
```

## HTTP Status Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `500` - Server Error

## Register in Service Provider

```php
<?php

namespace WPPB\Providers;

use WPPB\Abstracts\AbstractServiceProvider;
use WPPB\Api\PostsController;

class ApiServiceProvider extends AbstractServiceProvider {

	public function register(): void {
		$this->singleton(
			'api.posts',
			PostsController::class
		);
	}

	public function boot(): void {
		$this->container->get( 'api.posts' )->init();
	}
}
```

Add to Plugin class:

```php
protected array $providers = array(
	AdminServiceProvider::class,
	FrontendServiceProvider::class,
	ApiServiceProvider::class,
);
```

## Testing Endpoints

### cURL

```bash
# GET
curl https://site.com/wp-json/wppb/v1/posts

# POST
curl -X POST https://site.com/wp-json/wppb/v1/posts \
  -H "Content-Type: application/json" \
  -d '{"title":"Test"}'
```

### JavaScript

```javascript
fetch( '/wp-json/wppb/v1/posts' )
	.then( response => response.json() )
	.then( data => console.log( data ) );
```

## Rules

- Create controllers in `includes/api/`
- Use namespace format: `wppb/v1`
- Always implement permission callbacks
- Validate and sanitize all input
- Return proper HTTP status codes
- Use WP_Error for errors
- Register routes on `rest_api_init` hook
- Use Hooker service for hooks

