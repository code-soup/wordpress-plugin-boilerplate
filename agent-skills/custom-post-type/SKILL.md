---
name: custom-post-type
description: Register custom post types and taxonomies with proper labels, capabilities, and features. Use when creating custom content types like portfolios, testimonials, or products.
---

# Custom Post Type

Register custom post types and taxonomies.

## Create CPT Class

**File**: `includes/post-types/class-portfolio.php`

```php
<?php

namespace WPPB\PostTypes;

use WPPB\Core\Hooker;

defined( 'ABSPATH' ) || exit;

class Portfolio {

	private Hooker $hooker;

	public function __construct( Hooker $hooker ) {
		$this->hooker = $hooker;
	}

	public function init(): void {
		$this->hooker->add_action(
			'init',
			$this,
			'register_post_type'
		);

		$this->hooker->add_action(
			'init',
			$this,
			'register_taxonomy'
		);
	}

	public function register_post_type(): void {
		$labels = array(
			'name'          => __( 'Portfolio', '__PLUGIN_TEXTDOMAIN__' ),
			'singular_name' => __( 'Portfolio Item', '__PLUGIN_TEXTDOMAIN__' ),
			'add_new'       => __( 'Add New', '__PLUGIN_TEXTDOMAIN__' ),
			'add_new_item'  => __( 'Add New Item', '__PLUGIN_TEXTDOMAIN__' ),
			'edit_item'     => __( 'Edit Item', '__PLUGIN_TEXTDOMAIN__' ),
			'new_item'      => __( 'New Item', '__PLUGIN_TEXTDOMAIN__' ),
			'view_item'     => __( 'View Item', '__PLUGIN_TEXTDOMAIN__' ),
			'search_items'  => __( 'Search Items', '__PLUGIN_TEXTDOMAIN__' ),
			'not_found'     => __( 'No items found', '__PLUGIN_TEXTDOMAIN__' ),
		);

		$args = array(
			'labels'              => $labels,
			'public'              => true,
			'has_archive'         => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => true,
			'menu_icon'           => 'dashicons-portfolio',
			'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
			'rewrite'             => array( 'slug' => 'portfolio' ),
			'capability_type'     => 'post',
			'hierarchical'        => false,
		);

		register_post_type( 'wppb_portfolio', $args );
	}

	public function register_taxonomy(): void {
		$labels = array(
			'name'          => __( 'Categories', '__PLUGIN_TEXTDOMAIN__' ),
			'singular_name' => __( 'Category', '__PLUGIN_TEXTDOMAIN__' ),
			'search_items'  => __( 'Search Categories', '__PLUGIN_TEXTDOMAIN__' ),
			'all_items'     => __( 'All Categories', '__PLUGIN_TEXTDOMAIN__' ),
			'edit_item'     => __( 'Edit Category', '__PLUGIN_TEXTDOMAIN__' ),
			'update_item'   => __( 'Update Category', '__PLUGIN_TEXTDOMAIN__' ),
			'add_new_item'  => __( 'Add New Category', '__PLUGIN_TEXTDOMAIN__' ),
		);

		$args = array(
			'labels'            => $labels,
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'portfolio-category' ),
		);

		register_taxonomy( 'wppb_portfolio_cat', array( 'wppb_portfolio' ), $args );
	}
}
```

## Common Arguments

### Post Type

```php
array(
	'public'             => true,           // Public visibility
	'has_archive'        => true,           // Archive page
	'show_in_rest'       => true,           // Gutenberg support
	'menu_icon'          => 'dashicons-*',  // Admin menu icon
	'supports'           => array(          // Features
		'title',
		'editor',
		'thumbnail',
		'excerpt',
		'custom-fields',
	),
	'rewrite'            => array(
		'slug' => 'custom-slug',
	),
	'capability_type'    => 'post',         // Capabilities
	'hierarchical'       => false,          // Like pages (true) or posts (false)
)
```

### Taxonomy

```php
array(
	'hierarchical'      => true,   // Like categories (true) or tags (false)
	'public'            => true,
	'show_ui'           => true,
	'show_admin_column' => true,   // Show in admin list
	'show_in_rest'      => true,   // Gutenberg support
	'rewrite'           => array(
		'slug' => 'custom-tax',
	),
)
```

## Meta Boxes

```php
public function init(): void {
	$this->hooker->add_actions(
		$this,
		array(
			'init',
			'add_meta_boxes',
			'save_post',
		)
	);
}

public function add_meta_boxes(): void {
	add_meta_box(
		'wppb_portfolio_details',
		__( 'Portfolio Details', '__PLUGIN_TEXTDOMAIN__' ),
		array( $this, 'render_meta_box' ),
		'wppb_portfolio',
		'normal',
		'default'
	);
}

public function render_meta_box( $post ): void {
	wp_nonce_field( 'wppb_portfolio_meta', 'wppb_portfolio_nonce' );

	$url = get_post_meta( $post->ID, '_portfolio_url', true );
	?>
	<label><?php esc_html_e( 'URL:', '__PLUGIN_TEXTDOMAIN__' ); ?></label>
	<input type="text" name="portfolio_url" value="<?php echo esc_attr( $url ); ?>" />
	<?php
}

public function save_post( $post_id ): void {
	if ( ! isset( $_POST['wppb_portfolio_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_POST['wppb_portfolio_nonce'], 'wppb_portfolio_meta' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['portfolio_url'] ) ) {
		update_post_meta(
			$post_id,
			'_portfolio_url',
			sanitize_url( $_POST['portfolio_url'] )
		);
	}
}
```

## Custom Columns

```php
public function init(): void {
	$this->hooker->add_filter(
		'manage_wppb_portfolio_posts_columns',
		$this,
		'add_columns'
	);

	$this->hooker->add_action(
		'manage_wppb_portfolio_posts_custom_column',
		$this,
		'render_column',
		10,
		2
	);
}

public function add_columns( array $columns ): array {
	$columns['portfolio_url'] = __( 'URL', '__PLUGIN_TEXTDOMAIN__' );
	return $columns;
}

public function render_column( string $column, int $post_id ): void {
	if ( 'portfolio_url' === $column ) {
		$url = get_post_meta( $post_id, '_portfolio_url', true );
		echo esc_html( $url );
	}
}
```

## Register in Service Provider

```php
<?php

namespace WPPB\Providers;

use WPPB\Abstracts\AbstractServiceProvider;
use WPPB\PostTypes\Portfolio;

class PostTypeServiceProvider extends AbstractServiceProvider {

	public function register(): void {
		$this->singleton(
			'post_types.portfolio',
			Portfolio::class
		);
	}

	public function boot(): void {
		$this->container->get( 'post_types.portfolio' )->init();
	}
}
```

## Flush Rewrite Rules

After registering CPT, flush rewrite rules once:

```php
// In Activator class
public static function activate(): void {
	// Register CPT
	$portfolio = new Portfolio( new Hooker() );
	$portfolio->register_post_type();

	// Flush
	flush_rewrite_rules();
}
```

## Rules

- Create in `includes/post-types/`
- Use plugin prefix in post type slug
- Register on `init` hook
- Use Hooker service
- Make strings translatable
- Set `show_in_rest` for Gutenberg
- Flush rewrite rules on activation
- Use proper capability checks

