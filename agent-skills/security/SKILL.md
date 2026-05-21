---
name: security
description: Security best practices - escape output, sanitize input, verify nonces, check capabilities. Use when handling user input, displaying data, or performing privileged operations.
---

# Security

Security best practices for WordPress plugin development.

## Escape Output

Always escape data before displaying.

### HTML Content

```php
echo esc_html( $text );
echo esc_html( $user_input );
echo esc_html( get_option( 'setting' ) );
```

### HTML Attributes

```php
<input type="text" value="<?php echo esc_attr( $value ); ?>" />
<div class="<?php echo esc_attr( $class ); ?>">
<a href="#" data-id="<?php echo esc_attr( $id ); ?>">
```

### URLs

```php
<a href="<?php echo esc_url( $url ); ?>">Link</a>
<img src="<?php echo esc_url( $image_url ); ?>" />
```

### JavaScript

```php
<script>
var data = <?php echo wp_json_encode( $data ); ?>;
</script>
```

### Textarea

```php
<textarea><?php echo esc_textarea( $content ); ?></textarea>
```

### Allow Some HTML

```php
echo wp_kses_post( $content ); // Allows post content HTML
echo wp_kses( $content, $allowed_tags ); // Custom allowed tags
```

Custom allowed tags:

```php
$allowed = array(
	'a'      => array(
		'href'  => array(),
		'title' => array(),
	),
	'strong' => array(),
	'em'     => array(),
);

echo wp_kses( $content, $allowed );
```

## Sanitize Input

Always sanitize user input.

### Text Field

```php
$name = sanitize_text_field( $_POST['name'] );
```

### Email

```php
$email = sanitize_email( $_POST['email'] );
```

### URL

```php
$url = sanitize_url( $_POST['url'] );
// or
$url = esc_url_raw( $_POST['url'] );
```

### Textarea

```php
$content = sanitize_textarea_field( $_POST['content'] );
```

### HTML Content

```php
$content = wp_kses_post( $_POST['content'] );
```

### Integer

```php
$id = absint( $_POST['id'] );
$count = intval( $_POST['count'] );
```

### Array

```php
$items = array_map( 'sanitize_text_field', $_POST['items'] );
```

### Key/Slug

```php
$key = sanitize_key( $_POST['key'] );
```

### File Name

```php
$filename = sanitize_file_name( $_FILES['file']['name'] );
```

## Nonce Verification

### Create Nonce

```php
$nonce = wp_create_nonce( 'wppb_action' );
```

### Verify Nonce (Form)

```php
if ( ! isset( $_POST['wppb_nonce'] ) ) {
	wp_die( esc_html__( 'Nonce missing', '__PLUGIN_TEXTDOMAIN__' ) );
}

if ( ! wp_verify_nonce( $_POST['wppb_nonce'], 'wppb_action' ) ) {
	wp_die( esc_html__( 'Invalid nonce', '__PLUGIN_TEXTDOMAIN__' ) );
}
```

### Verify Nonce (AJAX)

```php
if ( ! check_ajax_referer( 'wppb_ajax_nonce', 'nonce', false ) ) {
	wp_send_json_error(
		array(
			'message' => __( 'Invalid nonce', '__PLUGIN_TEXTDOMAIN__' ),
		),
		403
	);
}
```

### Nonce Field (Form)

```php
<form method="post">
	<?php wp_nonce_field( 'wppb_action', 'wppb_nonce' ); ?>
	<input type="text" name="name" />
	<button type="submit">Submit</button>
</form>
```

### Nonce URL

```php
$url = wp_nonce_url(
	admin_url( 'admin.php?page=wppb&action=delete&id=123' ),
	'wppb_delete'
);
```

Verify:

```php
if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'wppb_delete' ) ) {
	wp_die( esc_html__( 'Invalid nonce', '__PLUGIN_TEXTDOMAIN__' ) );
}
```

## Capability Checks

### Admin Only

```php
if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( esc_html__( 'Permission denied', '__PLUGIN_TEXTDOMAIN__' ) );
}
```

### Edit Posts

```php
if ( ! current_user_can( 'edit_posts' ) ) {
	wp_die( esc_html__( 'Permission denied', '__PLUGIN_TEXTDOMAIN__' ) );
}
```

### Edit Specific Post

```php
if ( ! current_user_can( 'edit_post', $post_id ) ) {
	wp_die( esc_html__( 'Permission denied', '__PLUGIN_TEXTDOMAIN__' ) );
}
```

### Custom Capability

```php
if ( ! current_user_can( 'wppb_custom_capability' ) ) {
	wp_die( esc_html__( 'Permission denied', '__PLUGIN_TEXTDOMAIN__' ) );
}
```

## SQL Injection Prevention

### Use $wpdb Prepare

```php
global $wpdb;

$results = $wpdb->get_results(
	$wpdb->prepare(
		"SELECT * FROM {$wpdb->prefix}table WHERE id = %d AND name = %s",
		$id,
		$name
	)
);
```

### Placeholders

- `%d` - Integer
- `%f` - Float
- `%s` - String

### Never Use Direct Input

```php
// WRONG - SQL Injection vulnerability
$wpdb->query( "SELECT * FROM table WHERE id = {$_GET['id']}" );

// CORRECT
$wpdb->get_results(
	$wpdb->prepare(
		"SELECT * FROM {$wpdb->prefix}table WHERE id = %d",
		absint( $_GET['id'] )
	)
);
```

## Complete Secure Form Example

```php
<?php

namespace WPPB\Admin;

use WPPB\Core\Hooker;

class SecureForm {

	private Hooker $hooker;

	public function __construct( Hooker $hooker ) {
		$this->hooker = $hooker;
	}

	public function init(): void {
		$this->hooker->add_action(
			'admin_post_wppb_submit',
			$this,
			'handle_submit'
		);
	}

	public function render(): void {
		?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php wp_nonce_field( 'wppb_form', 'wppb_nonce' ); ?>
			<input type="hidden" name="action" value="wppb_submit" />

			<label><?php esc_html_e( 'Name:', '__PLUGIN_TEXTDOMAIN__' ); ?></label>
			<input type="text" name="name" value="<?php echo esc_attr( $name ); ?>" />

			<label><?php esc_html_e( 'Email:', '__PLUGIN_TEXTDOMAIN__' ); ?></label>
			<input type="email" name="email" value="<?php echo esc_attr( $email ); ?>" />

			<button type="submit">
				<?php esc_html_e( 'Submit', '__PLUGIN_TEXTDOMAIN__' ); ?>
			</button>
		</form>
		<?php
	}

	public function handle_submit(): void {
		// Check nonce
		if ( ! isset( $_POST['wppb_nonce'] ) || ! wp_verify_nonce( $_POST['wppb_nonce'], 'wppb_form' ) ) {
			wp_die( esc_html__( 'Invalid nonce', '__PLUGIN_TEXTDOMAIN__' ) );
		}

		// Check capability
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Permission denied', '__PLUGIN_TEXTDOMAIN__' ) );
		}

		// Sanitize input
		$name  = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
		$email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';

		// Validate
		if ( empty( $name ) || empty( $email ) ) {
			wp_die( esc_html__( 'Required fields missing', '__PLUGIN_TEXTDOMAIN__' ) );
		}

		// Process
		update_option( 'wppb_name', $name );
		update_option( 'wppb_email', $email );

		// Redirect
		wp_safe_redirect(
			add_query_arg(
				'message',
				'success',
				admin_url( 'admin.php?page=wppb' )
			)
		);
		exit;
	}
}
```

## Rules

- Escape all output
- Sanitize all input
- Verify nonces for forms and AJAX
- Check user capabilities
- Use $wpdb->prepare for SQL queries
- Never trust user input
- Use wp_safe_redirect for redirects
- Add `defined( 'ABSPATH' ) || exit;` to all files

