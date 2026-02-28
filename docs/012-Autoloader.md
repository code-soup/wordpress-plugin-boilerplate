# Custom Autoloader

This boilerplate uses a custom autoloader that bridges the gap between modern namespaced PHP architecture and WordPress Coding Standards file naming conventions.

## Why a Custom Autoloader?

WordPress Coding Standards require specific file naming conventions that are incompatible with PSR-4 autoloading:

- **WordPress Standard**: `class-container.php` for class `Container`
- **PSR-4 Standard**: `Container.php` for class `Container`

The custom autoloader allows you to:
1. Maintain modern namespaced class structure
2. Comply with WordPress Coding Standards
3. Have automatic class loading without manual `require` statements

## File Naming Conventions

The autoloader expects files to follow these WordPress naming patterns:

### Classes
- **Format**: `class-{name}.php`
- **Example**: `WPPB\Core\Container` → `includes/core/class-container.php`
- **Example**: `WPPB\Admin\Init` → `includes/admin/class-init.php`

### Traits
- **Format**: `trait-{name}.php`
- **Example**: `WPPB\Traits\HelpersTrait` → `includes/traits/trait-helpers.php`
- **Example**: `WPPB\Traits\ValidationTrait` → `includes/traits/trait-validation.php`

### Interfaces
- **Format**: `interface-{name}.php`
- **Example**: `WPPB\Interfaces\ServiceProviderInterface` → `includes/interfaces/interface-service-provider.php`

### Abstract Classes
- **Format**: `class-abstract-{name}.php`
- **Example**: `WPPB\Abstracts\AbstractServiceProvider` → `includes/abstracts/class-abstract-service-provider.php`

## Name Conversion Rules

The autoloader automatically converts CamelCase class names to kebab-case filenames:

| Class Name | Filename |
|------------|----------|
| `Container` | `class-container.php` |
| `ServiceProvider` | `class-service-provider.php` |
| `AdminServiceProvider` | `class-admin-service-provider.php` |
| `ValidationTrait` | `trait-validation.php` |
| `I18n` | `class-i18n.php` |

## Namespace to Directory Mapping

The autoloader maps namespaces to directories:

```php
WPPB\Core\         → includes/core/
WPPB\Admin\        → includes/admin/
WPPB\Frontend\     → includes/frontend/
WPPB\Providers\    → includes/providers/
WPPB\Abstracts\    → includes/abstracts/
WPPB\Interfaces\   → includes/interfaces/
WPPB\Traits\       → includes/traits/
```

## How It Works

When you reference a class like `WPPB\Core\Container`, the autoloader:

1. Detects the namespace: `WPPB\Core\`
2. Maps to directory: `includes/core/`
3. Extracts class name: `Container`
4. Determines file type: Class (not trait/interface)
5. Converts to kebab-case: `container`
6. Adds prefix: `class-`
7. Adds extension: `.php`
8. Final path: `includes/core/class-container.php`

## Implementation

The autoloader is located at `includes/autoloader.php` and is registered in `run.php`:

```php
// Load composer autoloader for dependencies.
require 'vendor/autoload.php';

// Register custom autoloader for WordPress-style filenames.
\WPPB\Autoloader::register( __DIR__ );
```

## Adding New Namespaces

To add a new namespace mapping, edit `includes/autoloader.php`:

```php
private static $namespace_map = array(
	'WPPB\\Core\\'       => 'includes/core/',
	'WPPB\\Admin\\'      => 'includes/admin/',
	'WPPB\\Frontend\\'   => 'includes/frontend/',
	'WPPB\\Providers\\'  => 'includes/providers/',
	'WPPB\\Abstracts\\'  => 'includes/abstracts/',
	'WPPB\\Interfaces\\' => 'includes/interfaces/',
	'WPPB\\Traits\\'     => 'includes/traits/',
	'WPPB\\MyFeature\\'  => 'includes/my-feature/', // New namespace
);
```

## Best Practices

1. **Always use namespaces** - Never create classes in the global namespace
2. **Follow naming conventions** - Use CamelCase for class names, they'll be converted automatically
3. **Organize by feature** - Create logical namespace groupings (e.g., `WPPB\Integrations\`, `WPPB\Services\`)
4. **Don't manually require files** - Let the autoloader handle it
5. **Keep directory structure flat** - Avoid deep nesting within namespace directories

## Troubleshooting

### Class not found error

If you get a "Class not found" error:

1. Verify the filename matches the expected pattern
2. Check that the namespace is correctly mapped in `$namespace_map`
3. Ensure the file exists in the correct directory
4. Run `composer dump-autoload` to refresh the autoloader

### Example debugging

For class `WPPB\Services\EmailService`:
- Expected file: `includes/services/class-email-service.php`
- Check namespace map includes: `'WPPB\\Services\\' => 'includes/services/'`
- Verify file exists at the expected path
- Check class name matches: `class EmailService` (not `class Email_Service`)

## Migration from PSR-4

If migrating from PSR-4 autoloading:

1. Rename all files to WordPress conventions
2. Update `composer.json` to load the custom autoloader
3. Run `composer dump-autoload`
4. Test that all classes load correctly
5. Update documentation references to new filenames

