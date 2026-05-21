---
name: file-naming
description: File naming conventions and namespace-to-directory mapping for PHP files. Use when creating new classes, traits, interfaces, or abstract classes to ensure proper autoloading.
---

# File Naming Conventions

Strict naming conventions for autoloader compatibility.

## Classes

**Pattern**: `class-{kebab-case-name}.php`

```
SettingsPage     → class-settings-page.php
PostController   → class-post-controller.php
EmailService     → class-email-service.php
AjaxHandler      → class-ajax-handler.php
```

## Traits

**Pattern**: `trait-{kebab-case-name}.php`

```
LoggingTrait     → trait-logging.php
LoggingTrait     → trait-logging.php
CachingTrait     → trait-caching.php
LoggingTrait     → trait-logging.php
```

## Interfaces

**Pattern**: `interface-{kebab-case-name}.php`

```
ServiceProviderInterface → interface-service-provider.php
RepositoryInterface      → interface-repository.php
CacheInterface           → interface-cache.php
```

## Abstract Classes

**Pattern**: `class-abstract-{kebab-case-name}.php`

```
AbstractServiceProvider → class-abstract-service-provider.php
AbstractRepository      → class-abstract-repository.php
AbstractController      → class-abstract-controller.php
```

## Namespace to Directory

### Root Namespace

`WPPB` → `includes/`

### Subdirectories

```
WPPB\Core        → includes/core/
WPPB\Admin       → includes/admin/
WPPB\Frontend    → includes/frontend/
WPPB\Api         → includes/api/
WPPB\Ajax        → includes/ajax/
WPPB\Services    → includes/services/
WPPB\Providers   → includes/providers/
WPPB\PostTypes   → includes/post-types/
WPPB\Taxonomies  → includes/taxonomies/
WPPB\Traits      → includes/traits/
WPPB\Interfaces  → includes/interfaces/
WPPB\Abstracts   → includes/abstracts/
```

## Complete Examples

### Class

```php
<?php
/**
 * Settings Page
 *
 * @package WPPB
 */

namespace WPPB\Admin;

defined( 'ABSPATH' ) || exit;

class SettingsPage {
	// Class code
}
```

**File**: `includes/admin/class-settings-page.php`

### Trait

```php
<?php
/**
 * Caching trait
 *
 * @package WPPB
 */

namespace WPPB\Traits;

defined( 'ABSPATH' ) || exit;

trait CachingTrait {
	// Trait code
}
```

**File**: `includes/traits/trait-caching.php`

### Interface

```php
<?php
/**
 * Repository interface
 *
 * @package WPPB
 */

namespace WPPB\Interfaces;

defined( 'ABSPATH' ) || exit;

interface RepositoryInterface {
	// Interface code
}
```

**File**: `includes/interfaces/interface-repository.php`

### Abstract Class

```php
<?php
/**
 * Abstract repository
 *
 * @package WPPB
 */

namespace WPPB\Abstracts;

defined( 'ABSPATH' ) || exit;

abstract class AbstractRepository {
	// Abstract class code
}
```

**File**: `includes/abstracts/class-abstract-repository.php`

## Conversion Rules

### PascalCase to kebab-case

```
SettingsPage      → settings-page
PostController    → post-controller
EmailService      → email-service
AjaxHandler       → ajax-handler
FormValidator     → form-validator
```

### Multi-word Examples

```
CustomPostType         → class-custom-post-type.php
RestApiController      → class-rest-api-controller.php
AdminSettingsPage      → class-admin-settings-page.php
FrontendAssetManager   → class-frontend-asset-manager.php
```

## Directory Creation

When creating new namespace:

```bash
# Create directory
mkdir -p includes/services

# Create class
touch includes/services/class-email-service.php
```

Namespace must match:

```php
namespace WPPB\Services;

class EmailService {
	// Code
}
```

## Common Mistakes

### Wrong

```
class_settings_page.php     # Underscores
ClassSettingsPage.php       # PascalCase filename
settings-page.php           # Missing prefix
class-SettingsPage.php      # Mixed case
```

### Correct

```
class-settings-page.php
```

## Autoloader Requirements

For autoloader to work:

1. File must be in correct directory
2. Filename must match pattern
3. Namespace must match directory
4. Class name must match filename (converted)

Example:

```
Namespace: WPPB\Admin\SettingsPage
Directory: includes/admin/
Filename:  class-settings-page.php
```

## Rules

- Always use kebab-case for filenames
- Prefix classes with `class-`
- Prefix traits with `trait-`
- Prefix interfaces with `interface-`
- Prefix abstracts with `class-abstract-`
- Match namespace to directory structure
- Use lowercase for all filenames
- Use hyphens, never underscores
- Include file header with package
- Add `defined( 'ABSPATH' ) || exit;`

