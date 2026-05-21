---
name: i18n-translation
description: Make strings translatable using WordPress i18n functions. Use when displaying any user-facing text to ensure proper internationalization and localization support.
---

# Internationalization (i18n)

Make all user-facing strings translatable.

## Text Domain

Always use: `__PLUGIN_TEXTDOMAIN__`

## Translation Functions

### Basic Translation

```php
__( 'Text', '__PLUGIN_TEXTDOMAIN__' );
```

Returns translated string.

### Translate and Escape

```php
// For HTML content
esc_html__( 'Text', '__PLUGIN_TEXTDOMAIN__' );

// For attributes
esc_attr__( 'Text', '__PLUGIN_TEXTDOMAIN__' );

// For URLs
esc_url( __( 'https://example.com', '__PLUGIN_TEXTDOMAIN__' ) );
```

### Echo Translation

```php
// Echo translated text
_e( 'Text', '__PLUGIN_TEXTDOMAIN__' );

// Echo and escape HTML
esc_html_e( 'Text', '__PLUGIN_TEXTDOMAIN__' );

// Echo and escape attribute
esc_attr_e( 'Text', '__PLUGIN_TEXTDOMAIN__' );
```

## Placeholders with sprintf

### Single Placeholder

```php
sprintf(
	__( 'Hello %s', '__PLUGIN_TEXTDOMAIN__' ),
	$name
);
```

### Multiple Placeholders

```php
sprintf(
	__( 'Found %d items in %s', '__PLUGIN_TEXTDOMAIN__' ),
	$count,
	$location
);
```

### With Escaping

```php
echo esc_html(
	sprintf(
		__( 'Welcome %s', '__PLUGIN_TEXTDOMAIN__' ),
		$username
	)
);
```

## Pluralization

```php
sprintf(
	_n(
		'%d item',
		'%d items',
		$count,
		'__PLUGIN_TEXTDOMAIN__'
	),
	$count
);
```

Example:

```php
$count   = 5;
$message = sprintf(
	_n(
		'You have %d new message',
		'You have %d new messages',
		$count,
		'__PLUGIN_TEXTDOMAIN__'
	),
	$count
);
// Output: "You have 5 new messages"
```

## Context

When same word has different meanings:

```php
_x( 'Post', 'noun', '__PLUGIN_TEXTDOMAIN__' );
_x( 'Post', 'verb', '__PLUGIN_TEXTDOMAIN__' );
```

With escaping:

```php
esc_html_x( 'Post', 'noun', '__PLUGIN_TEXTDOMAIN__' );
```

## Complete Examples

### Admin Page

```php
public function render(): void {
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Settings', '__PLUGIN_TEXTDOMAIN__' ); ?></h1>
		<p>
			<?php
			echo esc_html(
				sprintf(
					__( 'Plugin version: %s', '__PLUGIN_TEXTDOMAIN__' ),
					$this->get_version()
				)
			);
			?>
		</p>
		<button type="submit">
			<?php esc_html_e( 'Save Changes', '__PLUGIN_TEXTDOMAIN__' ); ?>
		</button>
	</div>
	<?php
}
```

### Form Validation

```php
public function validate( array $data ): array {
	$errors = array();

	if ( empty( $data['name'] ) ) {
		$errors[] = __( 'Name is required', '__PLUGIN_TEXTDOMAIN__' );
	}

	if ( empty( $data['email'] ) ) {
		$errors[] = __( 'Email is required', '__PLUGIN_TEXTDOMAIN__' );
	}

	if ( ! empty( $errors ) ) {
		return array(
			'success' => false,
			'message' => sprintf(
				_n(
					'%d error found',
					'%d errors found',
					count( $errors ),
					'__PLUGIN_TEXTDOMAIN__'
				),
				count( $errors )
			),
			'errors'  => $errors,
		);
	}

	return array(
		'success' => true,
		'message' => __( 'Validation passed', '__PLUGIN_TEXTDOMAIN__' ),
	);
}
```

### AJAX Response

```php
public function handle_request(): void {
	wp_send_json_success(
		array(
			'message' => __( 'Data saved successfully', '__PLUGIN_TEXTDOMAIN__' ),
		)
	);
}
```

### JavaScript Localization

```php
$this->assets->localize_script(
	'wppb-admin',
	'wppbStrings',
	array(
		'confirm' => __( 'Are you sure?', '__PLUGIN_TEXTDOMAIN__' ),
		'success' => __( 'Success!', '__PLUGIN_TEXTDOMAIN__' ),
		'error'   => __( 'An error occurred', '__PLUGIN_TEXTDOMAIN__' ),
	)
);
```

JavaScript:

```javascript
if ( confirm( wppbStrings.confirm ) ) {
	alert( wppbStrings.success );
}
```

## Generate POT File

```bash
wp i18n make-pot . languages/plugin-name.pot
```

## Translation File Structure

```
languages/
├── plugin-name.pot          # Template
├── plugin-name-de_DE.po     # German translation
├── plugin-name-de_DE.mo     # German compiled
├── plugin-name-fr_FR.po     # French translation
└── plugin-name-fr_FR.mo     # French compiled
```

## Load Text Domain

Already handled in `includes/core/class-i18n.php`:

```php
public function load_plugin_textdomain(): void {
	load_plugin_textdomain(
		'__PLUGIN_TEXTDOMAIN__',
		false,
		dirname( plugin_basename( __FILE__ ), 2 ) . '/languages/'
	);
}
```

## Rules

- Always use `__PLUGIN_TEXTDOMAIN__` as text domain
- Use `__()` for returning translated strings
- Use `_e()` for echoing translated strings
- Use `esc_html__()` / `esc_html_e()` for HTML content
- Use `esc_attr__()` / `esc_attr_e()` for attributes
- Use `sprintf()` for placeholders
- Use `_n()` for pluralization
- Use `_x()` for context
- Never concatenate translated strings
- Keep full sentences together
- Don't translate variable names or code

