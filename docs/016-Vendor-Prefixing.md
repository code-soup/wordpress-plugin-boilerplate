# Vendor Prefixing with PHP-Scoper

## Table of Contents

- [Why Prefix Dependencies?](#why-prefix-dependencies)
- [When to Use Vendor Prefixing](#when-to-use-vendor-prefixing)
- [Setup Instructions](#setup-instructions)
- [Using PHP-Scoper](#using-php-scoper)
- [Alternative: Mozart](#alternative-mozart)
- [Workflow Integration](#workflow-integration)
- [Troubleshooting](#troubleshooting)

---

## Why Prefix Dependencies?

When you bundle Composer dependencies with your WordPress plugin, you risk **namespace collisions** with other plugins that use the same libraries.

### Example Problem

Two plugins both use `vlucas/phpdotenv` v5.6:

```
Plugin A: vendor/vlucas/phpdotenv
Plugin B: vendor/vlucas/phpdotenv
```

If Plugin A loads first, Plugin B might use Plugin A's version, causing:
- Version conflicts
- Fatal errors
- Unexpected behavior

### Solution: Namespace Prefixing

PHP-Scoper rewrites all vendor namespaces to be unique to your plugin:

```php
// Before scoping
use Dotenv\Dotenv;

// After scoping
use YourPlugin\Vendor\Dotenv\Dotenv;
```

---

## When to Use Vendor Prefixing

### ✅ Use Prefixing If:

1. **Distributing as a composer package** - Other plugins will install your plugin via composer
2. **Bundling common dependencies** - Using popular libraries like:
   - `vlucas/phpdotenv`
   - `guzzlehttp/guzzle`
   - `monolog/monolog`
   - `symfony/*` components
3. **Commercial plugin distribution** - Selling on marketplaces where conflicts are common

### ❌ Skip Prefixing If:

1. **Simple plugin** - No composer dependencies
2. **Unique dependencies** - Using niche libraries unlikely to conflict
3. **Development only** - Never distributed to other sites

---

## Setup Instructions

### Step 1: Install PHP-Scoper

```bash
composer require --dev humbug/php-scoper
```

### Step 2: Copy Configuration

```bash
cp scoper.inc.php.example scoper.inc.php
```

### Step 3: Configure Your Prefix

Edit `scoper.inc.php` and update the prefix:

```php
// Replace WPPB with your plugin namespace
$prefix = 'MyAwesomePlugin\\Vendor';
```

### Step 4: Run PHP-Scoper

```bash
vendor/bin/php-scoper add-prefix
```

This creates a `vendor-prefixed/` directory with scoped dependencies.

### Step 5: Update Autoloader

Edit `index.php`:

```php
// BEFORE scoping
require_once __DIR__ . '/vendor/autoload.php';

// AFTER scoping
require_once __DIR__ . '/vendor-prefixed/scoper-autoload.php';
```

---

## Using PHP-Scoper

### Full Workflow

```bash
# 1. Install dependencies
composer install --no-dev

# 2. Run scoper
vendor/bin/php-scoper add-prefix --output-dir=vendor-prefixed --force

# 3. Test plugin
# Verify all functionality works with prefixed vendors
```

### Build Script Example

Create `build.sh`:

```bash
#!/bin/bash

# Clean previous build
rm -rf vendor-prefixed

# Install production dependencies only
composer install --no-dev --optimize-autoloader

# Scope dependencies
vendor/bin/php-scoper add-prefix --force

# Reinstall dev dependencies
composer install

echo "Build complete! Plugin ready in vendor-prefixed/"
```

Make it executable:

```bash
chmod +x build.sh
./build.sh
```

---

## Alternative: Mozart

For WordPress-specific projects, **Mozart** is simpler than PHP-Scoper.

### Install Mozart

```bash
composer require --dev coenjacobs/mozart
```

### Create `mozart.json`

```json
{
  "dep_namespace": "MyPlugin\\Vendor\\",
  "dep_directory": "/vendor-prefixed/",
  "classmap_directory": "/classes/",
  "classmap_prefix": "MyPlugin_",
  "packages": [
    "vlucas/phpdotenv",
    "psr/container"
  ],
  "excluded_packages": [
    "composer/installers"
  ]
}
```

### Run Mozart

```bash
vendor/bin/mozart compose
```

---

## Workflow Integration

### Add Composer Scripts

Edit `composer.json`:

```json
"scripts": {
  "scope": [
    "@php vendor/bin/php-scoper add-prefix --output-dir=vendor-prefixed --force",
    "@composer dump-autoload"
  ],
  "scope:check": [
    "@php vendor/bin/php-scoper add-prefix --output-dir=vendor-prefixed --no-interaction"
  ]
}
```

Usage:

```bash
composer scope        # Run scoping
composer scope:check  # Dry run (check only)
```

### Update .gitignore

Add to `.gitignore`:

```
# Scoped vendor directory
vendor-prefixed/

# Scoper configuration (if customized)
scoper.inc.php
```

---

## Practical Example

### Scenario: Prefixing phpdotenv

Your plugin uses `vlucas/phpdotenv` which is commonly used by other plugins.

**Before Scoping:**

```php
<?php
namespace MyPlugin;

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
```

**After Running PHP-Scoper:**

The vendor code is automatically rewritten to:

```php
<?php
namespace MyPlugin\Vendor\Dotenv;

class Dotenv {
    // ... all code automatically prefixed
}
```

**Your Code Stays the Same:**

PHP-Scoper automatically updates your `use` statements:

```php
<?php
namespace MyPlugin;

// Automatically updated by scoper
use MyPlugin\Vendor\Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
```

**File Structure:**

```
your-plugin/
├── vendor/                  # Original (for development)
│   └── vlucas/phpdotenv/
├── vendor-prefixed/         # Scoped (for distribution)
│   ├── scoper-autoload.php  # Use this in production
│   └── vlucas/phpdotenv/    # Namespaces prefixed
└── scoper.inc.php           # Your configuration
```

---

## Troubleshooting

### Issue: "Class not found" after scoping

**Cause:** Still loading original autoloader

**Solution:**

```php
// Change this
require_once __DIR__ . '/vendor/autoload.php';

// To this
require_once __DIR__ . '/vendor-prefixed/scoper-autoload.php';
```

---

### Issue: WordPress functions prefixed

**Cause:** WordPress global functions detected as "to be scoped"

**Solution:** Check `scoper.inc.php` excludes:

```php
'exclude-functions' => [
    'add_action',
    'add_filter',
    '__',
    '_e',
    // Add more WordPress functions
],
```

---

### Issue: Constants not working

**Cause:** WordPress constants were prefixed

**Solution:** Add to `exclude-constants`:

```php
'exclude-constants' => [
    '/^ABSPATH$/',
    '/^WP_.*/',
    '/^WPINC$/',
],
```

---

### Issue: Scoper modifies too many files

**Cause:** Finders include unwanted directories

**Solution:** Update `exclude` in `scoper.inc.php`:

```php
'finders' => [
    Finder::create()
        ->exclude([
            'test',
            'tests',
            'docs',
            // Add more
        ])
        ->in('vendor'),
],
```

---

## Distribution Workflow

### For Public Plugins

**Option 1: Include scoped vendor in repo**

```bash
# Build scoped vendors
composer scope

# Commit vendor-prefixed/
git add vendor-prefixed/
git commit -m "Add scoped dependencies"
```

**Option 2: Build on deployment**

Add to CI/CD pipeline:

```yaml
# .github/workflows/build.yml
- name: Install dependencies
  run: composer install --no-dev

- name: Scope dependencies
  run: vendor/bin/php-scoper add-prefix --force
```

### For Private/Commercial Plugins

Create distribution package:

```bash
#!/bin/bash
# build-release.sh

VERSION="1.0.0"
PLUGIN_SLUG="my-awesome-plugin"

# Install and scope
composer install --no-dev
vendor/bin/php-scoper add-prefix --force

# Create zip (exclude dev files)
zip -r "${PLUGIN_SLUG}-${VERSION}.zip" \
  vendor-prefixed/ \
  includes/ \
  templates/ \
  dist/ \
  index.php \
  run.php \
  uninstall.php \
  -x "*.git*" "node_modules/*" "vendor/*"

echo "Release package: ${PLUGIN_SLUG}-${VERSION}.zip"
```

---

## Performance Considerations

### Build Time
- **PHP-Scoper**: Slower (rewrites all files)
- **Mozart**: Faster (simple copy + namespace replace)

### Runtime Performance
- **No difference** - Both produce standard PHP code
- Scoped vendors are just regular namespaced classes

### File Size
- Scoped vendors = **same size** as originals
- No compression or minification

---

## Best Practices

### ✅ Do

1. **Scope before distribution** - Not in development
2. **Test scoped version** - Run full test suite
3. **Document prefix** - Note it in README
4. **Exclude WordPress** - Never prefix WP functions
5. **Version control scoper config** - Track `scoper.inc.php`

### ❌ Don't

1. **Don't commit vendor-prefixed/** in development
2. **Don't scope WordPress core** - Causes conflicts
3. **Don't prefix PSR interfaces** (usually) - Can break interop
4. **Don't scope dev dependencies** - Only production packages
5. **Don't run scoper twice** - Creates nested prefixes

---

## Additional Resources

- [PHP-Scoper Documentation](https://github.com/humbug/php-scoper)
- [Mozart Documentation](https://github.com/coenjacobs/mozart)
- [Delicious Brains: PHP-Scoper Tutorial](https://deliciousbrains.com/php-scoper-namespace-composer-depencies/)
- [WordPress Plugin Conflicts Guide](https://roots.io/prefix-namespaces-in-wordpress-plugins-to-avoid-conflicts/)

---

## Summary

**Vendor prefixing** prevents namespace collisions when distributing WordPress plugins with composer dependencies.

**Quick Start:**
```bash
cp scoper.inc.php.example scoper.inc.php
composer require --dev humbug/php-scoper
vendor/bin/php-scoper add-prefix
```

**When to use:** Distributing plugins, bundling common libraries, commercial plugins

**When to skip:** Simple plugins, unique dependencies, development-only
