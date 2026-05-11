# Agent Skills - WordPress Plugin Boilerplate

This document provides instructions for AI agents and automated tools on how to properly use and extend this WordPress plugin boilerplate.

## Critical Rules

1. **File Naming**: All files MUST follow WordPress naming conventions (kebab-case with prefixes)
2. **No Direct Hooks**: NEVER use `add_action()` or `add_filter()` directly - always use Hooker service
3. **Array Syntax**: Use `array()` not `[]` for WordPress Coding Standards compliance
4. **Security**: Always escape output, sanitize input, verify nonces, check capabilities
5. **Text Domain**: Use `__PLUGIN_TEXTDOMAIN__` for all translatable strings
6. **Namespaces**: All classes must be in `WPPB\` namespace
7. **Service Providers**: Register all features through service providers
8. **Dependency Injection**: Use constructor injection for dependencies

---

## File Naming Conventions

### Classes
**Pattern**: `class-{kebab-case-name}.php`

Examples:
- `WPPB\Services\EmailService` → `includes/services/class-email-service.php`
- `WPPB\Admin\SettingsPage` → `includes/admin/class-settings-page.php`

### Traits
**Pattern**: `trait-{kebab-case-name}.php`

Examples:
- `WPPB\Traits\CachingTrait` → `includes/traits/trait-caching.php`

### Interfaces
**Pattern**: `interface-{kebab-case-name}.php`

Examples:
- `WPPB\Interfaces\RepositoryInterface` → `includes/interfaces/interface-repository.php`

### Abstract Classes
**Pattern**: `class-abstract-{kebab-case-name}.php`

Examples:
- `WPPB\Abstracts\AbstractRepository` → `includes/abstracts/class-abstract-repository.php`

---

## Namespace to Directory Mapping

```
WPPB\Core\         → includes/core/
WPPB\Admin\        → includes/admin/
WPPB\Frontend\     → includes/frontend/
WPPB\Services\     → includes/services/
WPPB\Models\       → includes/models/
WPPB\Repositories\ → includes/repositories/
WPPB\Integrations\ → includes/integrations/
WPPB\Api\          → includes/api/
WPPB\Database\     → includes/database/
```

---

## Class Template

```php
<?php
/**
 * {Class Description}
 *
 * File: includes/{namespace-path}/class-{kebab-name}.php
 *
 * @package WPPB
 */

declare(strict_types=1);

namespace WPPB\{Namespace};

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * {Class Name} class.
 *
 * @since 1.0.0
 */
class {ClassName} {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Constructor logic
	}
}
```

---

## Service Provider Template

**File**: `includes/providers/class-{feature}-service-provider.php`

```php
<?php
/**
 * {Feature} Service Provider.
 *
 * @package WPPB
 */

declare(strict_types=1);

namespace WPPB\Providers;

use WPPB\Abstracts\AbstractServiceProvider;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Service provider for {feature} functionality.
 */
class {Feature}ServiceProvider extends AbstractServiceProvider {

	/**
	 * Register the service provider.
	 */
	public function register(): void {
		$this->singleton( '{service_alias}', {YourService}::class );
	}

	/**
	 * Boot the service provider.
	 */
	public function boot(): void {
		$service = $this->container->get( '{service_alias}' );
		$service->init();
	}
}
```

**Register in**: `includes/core/class-plugin.php`

```php
protected array $providers = array(
	AdminServiceProvider::class,
	FrontendServiceProvider::class,
	\WPPB\Providers\{Feature}ServiceProvider::class,
);

### Multiple Hooks

```php
$this->hooker->add_actions(
	array(
		array( 'wp_enqueue_scripts', $this, 'enqueue_assets' ),
		array( 'admin_init', $this, 'admin_init', 20 ),
	)
);
```

---

## Enqueuing Assets

Use the Assets service.

```php
use WPPB\Core\Assets;

class MyService {

	private Assets $assets;

	public function __construct( Assets $assets ) {
		$this->assets = $assets;
	}

	public function enqueue_scripts(): void {
		$this->assets->enqueue_script(
			'my-script',
			'my-script.js',
			array( 'jquery' ),
			true
		);

		$this->assets->localize_script(
			'my-script',
			'myData',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'my_nonce' ),
			)
		);
	}

	public function enqueue_styles(): void {
		$this->assets->enqueue_style(
			'my-style',
			'my-style.css',
			array(),
			'all'
		);
	}
}
```

---

## AJAX Handler Template

**File**: `includes/ajax/class-{action}-handler.php`

```php
<?php
/**
 * {Action} AJAX Handler.
 *
 * @package WPPB
 */

declare(strict_types=1);

namespace WPPB\Ajax;

use WPPB\Core\Hooker;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Handles {action} AJAX requests.
 */
class {Action}Handler {

	private Hooker $hooker;

	public function __construct( Hooker $hooker ) {
		$this->hooker = $hooker;
	}

	public function init(): void {
		$this->hooker->add_action( 'wp_ajax_{action}', $this, 'handle' );
		$this->hooker->add_action( 'wp_ajax_nopriv_{action}', $this, 'handle' );
	}

	public function handle(): void {
		check_ajax_referer( '{action}_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized', '__PLUGIN_TEXTDOMAIN__' ) ) );
		}

		$data = filter_input( INPUT_POST, 'data', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		// Process data

		wp_send_json_success(
			array(
				'message' => __( 'Success', '__PLUGIN_TEXTDOMAIN__' ),
				'data'    => $data,
			)
		);
	}
}
```

---

## REST API Controller Template

**File**: `includes/api/class-{endpoint}-controller.php`

```php
<?php
/**
 * {Endpoint} REST Controller.
 *
 * @package WPPB
 */

declare(strict_types=1);

namespace WPPB\Api;

use WPPB\Core\Hooker;
use WP_REST_Request;
use WP_REST_Response;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * REST API controller for {endpoint}.
 */
class {Endpoint}Controller {

	private Hooker $hooker;

	public function __construct( Hooker $hooker ) {
		$this->hooker = $hooker;
	}

	public function init(): void {
		$this->hooker->add_action( 'rest_api_init', $this, 'register_routes' );
	}

	public function register_routes(): void {
		register_rest_route(
			'wppb/v1',
			'/{endpoint}',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_items' ),
				'permission_callback' => array( $this, 'check_permission' ),
			)
		);

		register_rest_route(
			'wppb/v1',
			'/{endpoint}/(?P<id>\d+)',
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
	}

	public function check_permission(): bool {
		return current_user_can( 'manage_options' );
	}

	public function get_items( WP_REST_Request $request ): WP_REST_Response {
		$items = array(); // Fetch items

		return new WP_REST_Response( $items, 200 );
	}

	public function get_item( WP_REST_Request $request ): WP_REST_Response {
		$id   = $request->get_param( 'id' );
		$item = array(); // Fetch item

		if ( empty( $item ) ) {
			return new WP_REST_Response(
				array( 'message' => __( 'Item not found', '__PLUGIN_TEXTDOMAIN__' ) ),
				404
			);
		}

		return new WP_REST_Response( $item, 200 );
	}
}
```

---

## Database Schema Template

**File**: `includes/database/class-{table}-schema.php`

```php
<?php
/**
 * {Table} Schema.
 *
 * @package WPPB
 */

declare(strict_types=1);

namespace WPPB\Database;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Schema for {table} table.
 */
class {Table}Schema {

	public static function create(): void {
		global $wpdb;

		$table_name      = $wpdb->prefix . 'wppb_{table_name}';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table_name} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			user_id bigint(20) unsigned NOT NULL,
			data longtext NOT NULL,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY user_id (user_id)
		) {$charset_collate};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	public static function drop(): void {
		global $wpdb;
		$table_name = $wpdb->prefix . 'wppb_{table_name}';
		$wpdb->query( "DROP TABLE IF EXISTS {$table_name}" );
	}
}
```

**Call from Activator**:

```php
// In includes/core/class-activator.php
use WPPB\Database\{Table}Schema;

public static function activate(): void {
	{Table}Schema::create();
}
```


```

---

## Repository Template

**File**: `includes/repositories/class-{entity}-repository.php`

```php
<?php
/**
 * {Entity} Repository.
 *
 * @package WPPB
 */

declare(strict_types=1);

namespace WPPB\Repositories;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Repository for {entity} operations.
 */
class {Entity}Repository {

	private function get_table_name(): string {
		global $wpdb;
		return $wpdb->prefix . 'wppb_{table_name}';
	}

	public function find( int $id ): ?object {
		global $wpdb;

		return $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$this->get_table_name()} WHERE id = %d",
				$id
			)
		);
	}

	public function create( array $data ) {
		global $wpdb;

		$wpdb->insert(
			$this->get_table_name(),
			$data,
			array( '%s', '%s' )
		);

		return $wpdb->insert_id;
	}

	public function update( int $id, array $data ): bool {
		global $wpdb;

		return (bool) $wpdb->update(
			$this->get_table_name(),
			$data,
			array( 'id' => $id ),
			array( '%s', '%s' ),
			array( '%d' )
		);
	}

	public function delete( int $id ): bool {
		global $wpdb;

		return (bool) $wpdb->delete(
			$this->get_table_name(),
			array( 'id' => $id ),
			array( '%d' )
		);
	}
}
```

---

## Admin Page Template

**File**: `includes/admin/class-{page}-page.php`

```php
<?php
/**
 * {Page} Admin Page.
 *
 * @package WPPB
 */

declare(strict_types=1);

namespace WPPB\Admin;

use WPPB\Core\Hooker;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Admin page for {page}.
 */
class {Page}Page {

	private Hooker $hooker;

	public function __construct( Hooker $hooker ) {
		$this->hooker = $hooker;
	}

	public function init(): void {
		$this->hooker->add_action( 'admin_menu', $this, 'add_menu_page' );
		$this->hooker->add_action( 'admin_init', $this, 'register_settings' );
	}

	public function add_menu_page(): void {
		add_menu_page(
			__( '{Page Title}', '__PLUGIN_TEXTDOMAIN__' ),
			__( '{Menu Title}', '__PLUGIN_TEXTDOMAIN__' ),
			'manage_options',
			'wppb-{page-slug}',
			array( $this, 'render_page' ),
			'dashicons-admin-generic',
			30
		);
	}

	public function register_settings(): void {
		register_setting(
			'wppb_{page}_settings',
			'wppb_{option_name}',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '',
			)
		);
	}

	public function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'wppb_{page}_settings' );
				do_settings_sections( 'wppb-{page-slug}' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}
}
```

---

## Security Checklist

When generating code, ensure:

1. **Nonce Verification**
```php
check_ajax_referer( 'my_nonce', 'nonce' );
wp_verify_nonce( $_POST['nonce'], 'my_action' );
```

2. **Capability Checks**
```php
if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( __( 'Unauthorized', '__PLUGIN_TEXTDOMAIN__' ) );
}
```

3. **Input Sanitization**
```php
$text  = sanitize_text_field( $_POST['text'] );
$email = sanitize_email( $_POST['email'] );
$url   = esc_url_raw( $_POST['url'] );
$int   = absint( $_POST['number'] );
```

4. **Output Escaping**
```php
echo esc_html( $text );
echo esc_attr( $attribute );
echo esc_url( $url );
echo wp_kses_post( $html );
```

5. **Database Queries**
```php
$wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $id );
```

---

## Code Standards Checklist

- [ ] File named with WordPress conventions (kebab-case with prefix)
- [ ] Namespace declared correctly
- [ ] `defined( 'ABSPATH' ) || exit;` at top
- [ ] `declare(strict_types=1);` included
- [ ] PHPDoc blocks for all methods
- [ ] Use `array()` not `[]`
- [ ] All strings translatable with `__PLUGIN_TEXTDOMAIN__`
- [ ] No direct `add_action`/`add_filter` calls
- [ ] Dependency injection used
- [ ] Security checks in place
- [ ] Input sanitized, output escaped

---

## Quick Reference

### Available Core Services

```php
$hooker = $this->container->get( 'hooker' );  // WPPB\Core\Hooker
$assets = $this->container->get( 'assets' );  // WPPB\Core\Assets
$i18n   = $this->container->get( 'i18n' );    // WPPB\Core\I18n
```

### Available Traits

```php
use WPPB\Traits\LoggingTrait;           // Debug logging
use WPPB\Traits\RequirementChecksTrait; // Version checks
```

### Common Patterns

**Get plugin instance**:
```php
$plugin = \WPPB\plugin();
```

**Get service from plugin**:
```php
$service = \WPPB\plugin()->get( 'service_alias' );
```

**Register service in provider**:
```php
$this->singleton( 'alias', ClassName::class );
```

---

## Testing

When generating code, also generate corresponding test structure:

**File**: `tests/unit/test-{class-name}.php`

```php
<?php
/**
 * Tests for {ClassName}.
 *
 * @package WPPB
 */

class Test_{ClassName} extends WP_UnitTestCase {

	public function test_method_name() {
		$instance = new \WPPB\Namespace\{ClassName}();
		$result   = $instance->method();

		$this->assertEquals( 'expected', $result );
	}
}
```

---

---

## Webpack Configuration Skill

### Adding Entry Points

**Location**: `src/config/config.user.js`

Entry points define which files webpack bundles together.

```javascript
const config = {
	entry: {
		"admin-common": ["./scripts/admin.js", "./styles/admin.scss"],
		"frontend-common": ["./scripts/main.js", "./styles/main.scss"],

		// Add new entry point
		"custom-bundle": ["./scripts/custom.js", "./styles/custom.scss"],
	},

	paths: pathUtils.paths,
	publicPath: publicPath,
};
```

**Output Structure**:
```
dist/
├── scripts/
│   ├── admin-common.js
│   ├── frontend-common.js
│   └── custom-bundle.js
└── styles/
    ├── admin-common.css
    ├── frontend-common.css
    └── custom-bundle.css
```

---

### Adding Custom Loader

**Step 1**: Create loader file in `src/config/webpack/loaders/`

**File**: `src/config/webpack/loaders/custom.js`

```javascript
/**
 * Custom loader configuration
 */
export default (config, env) => ({
	test: /\.custom$/,
	use: [
		{
			loader: 'custom-loader',
			options: {
				// loader options
			},
		},
	],
});
```

**Step 2**: Import in `src/config/webpack/config.module.js`

```javascript
import preLoaders from './loaders/pre.js';
import scriptLoaders from './loaders/scripts.js';
import styleLoaders from './loaders/styles.js';
import assetLoaders from './loaders/assets.js';
import customLoaders from './loaders/custom.js'; // Add import

export default (config, env) => {
	return {
		rules: [
			preLoaders(config, env),
			scriptLoaders(config, env),
			styleLoaders(config, env),
			assetLoaders(config, env),
			customLoaders(config, env), // Add loader
		],
	};
};
```

---

### Adding Custom Plugin

**Location**: `src/config/webpack/config.plugins.js`

```javascript
import CustomPlugin from 'custom-webpack-plugin';
import { conditionalPlugins } from '../util/plugins.js';

export default (config, env, fileName) => {
	const basePlugins = [
		// ... existing base plugins
	];

	const contextPlugins = conditionalPlugins([
		// ... existing conditional plugins

		// Add custom plugin
		{
			condition: true, // or env.isProduction, env.isWatching, etc.
			factory: () =>
				new CustomPlugin({
					option1: 'value1',
					option2: env.isProduction ? 'prod' : 'dev',
				}),
		},
	]);

	return [...basePlugins, ...contextPlugins];
};
```

---

### Modifying Babel Configuration

**Location**: `src/config/webpack/loaders/scripts.js`

```javascript
export default () => ({
	test: /\.[jt]sx?$/,
	exclude: [/node_modules/],
	use: [
		{
			loader: 'babel-loader',
			options: {
				cacheDirectory: true,
				presets: [
					'@babel/preset-env',
					'@babel/preset-react',      // Add React support
					'@babel/preset-typescript',  // Add TypeScript support
				],
				plugins: [
					'@babel/plugin-proposal-class-properties',
					'@babel/plugin-transform-runtime',
				],
			},
		},
	],
});
```

---

### Adding PostCSS Plugin

**Location**: `src/config/webpack/loaders/styles.js`

```javascript
{
	loader: 'postcss-loader',
	options: {
		postcssOptions: {
			plugins: [
				['postcss-preset-env', {
					stage: 3,
					features: {
						'nesting-rules': true,
					},
				}],
				'autoprefixer',           // Add autoprefixer
				'postcss-custom-media',   // Add custom media queries
			],
		},
		sourceMap: true,
	},
},
```

---

### Modifying Output Configuration

**Location**: `src/config/config.webpack.js`

```javascript
const baseConfig = {
	entry: userConfig.entry,
	context: userConfig.paths.src,
	output: {
		path: userConfig.paths.dist,
		publicPath: userConfig.publicPath,
		filename: `scripts/${fileName}.js`,
		clean: true,
		chunkFilename: `scripts/${isProduction ? '[id].[contenthash]' : '[id]'}.chunk.js`,

		// Add custom output options
		library: 'MyLibrary',
		libraryTarget: 'umd',
		globalObject: 'this',
	},
	// ... rest of config
};
```

---

### Adding Webpack Alias

**Location**: `src/config/config.webpack.js`

```javascript
resolve: {
	modules: [userConfig.paths.src, 'node_modules'],
	extensions: ['.js', '.jsx', '.ts', '.tsx', '.json'],
	enforceExtension: false,
	alias: {
		'@': userConfig.paths.src,
		'@components': pathUtils.fromSrc('scripts/components'),
		'@utils': pathUtils.fromSrc('scripts/utils'),
		'@styles': userConfig.paths.styles,
	},
	// ... rest of resolve config
},
```

**Usage in code**:
```javascript
// Instead of
import Component from '../../../components/Component';

// Use
import Component from '@components/Component';
```

---

### Modifying Dev Server Configuration

**Location**: `src/config/webpack/config.watch.js`

```javascript
return {
	devServer: {
		host: devServerInfo.host,
		port: devServerPort,
		hot: true,
		compress: true,
		allowedHosts: "all",

		// Add custom headers
		headers: {
			'Access-Control-Allow-Origin': '*',
			'Access-Control-Allow-Methods': 'GET, POST, PUT, DELETE, PATCH, OPTIONS',
		},

		// Add custom middleware
		onBeforeSetupMiddleware: (devServer) => {
			devServer.app.get('/custom-endpoint', (req, res) => {
				res.json({ message: 'Custom endpoint' });
			});
		},

		// Watch additional files
		watchFiles: {
			paths: [
				`${config.paths.root}/templates/**/*.php`,
				`${config.paths.root}/includes/**/*.php`,
				`${config.paths.root}/custom/**/*.php`, // Add custom path
			],
			options: {
				usePolling: false,
			},
		},

		// ... rest of devServer config
	},
};
```

---

### Adding Environment-Specific Configuration

**Location**: `src/config/util/env.js`

```javascript
// Add custom environment flag
export const isCustomMode = !!process.env.CUSTOM_MODE;
export const customValue = process.env.CUSTOM_VALUE || 'default';

// Use in webpack config
export const getCustomConfig = (isProduction) => {
	if (isCustomMode) {
		return {
			// custom mode config
		};
	}
	return isProduction ? prodConfig : devConfig;
};
```

**Set in package.json**:
```json
{
	"scripts": {
		"build:custom": "CUSTOM_MODE=true webpack --mode production --config src/config/config.webpack.js"
	}
}
```

---

### Webpack Configuration Checklist

When modifying webpack configuration:

- [ ] Changes made in appropriate modular file (not monolithic config)
- [ ] Loaders added to `loaders/` directory and imported in `config.module.js`
- [ ] Plugins use `conditionalPlugins` utility for conditional loading
- [ ] Environment-specific logic uses `env.js` utilities
- [ ] Paths use `paths.js` utilities
- [ ] New npm scripts added to `package.json` if needed
- [ ] `.env.local` updated if new environment variables added
- [ ] Build tested in both development and production modes

---

## Summary for AI Agents

When extending this boilerplate:

1. **Always** use WordPress file naming conventions
2. **Always** use service providers for registration
3. **Always** use Hooker service for WordPress hooks
4. **Always** use dependency injection
5. **Always** follow security best practices
6. **Always** use `array()` syntax
7. **Always** make strings translatable
8. **Never** use direct `add_action`/`add_filter`
9. **Never** use `[]` array syntax
10. **Never** skip security checks

**For Webpack Configuration**:
1. **Always** use modular configuration files in `src/config/`
2. **Always** add loaders to `loaders/` directory
3. **Always** use `conditionalPlugins` for plugin loading
4. **Always** use path utilities from `util/paths.js`
5. **Always** use environment utilities from `util/env.js`
6. **Never** create monolithic webpack.config.js
7. **Never** hardcode paths

This ensures generated code is consistent, secure, and maintainable.


