# Traits

Traits are a mechanism for code reuse in single-inheritance languages like PHP. In this boilerplate, they are used to group related, reusable methods that can be easily added to different classes.

This approach is preferred over maintaining a large, procedural `helpers.php` file, as it keeps code organized and contextually relevant.

## Available Traits

-   `LoggingTrait`: Contains methods for logging debug information.
-   `RequirementChecksTrait`: Helper methods for checking WordPress and PHP version requirements. Throws exceptions on failure.

## Using an Existing Trait

To use a trait, include the `use` statement inside your class definition. This will make the trait's methods available on the class instance as if they were defined directly in that class.

## Creating a New Trait

You can create your own traits to group functions for a specific purpose.

1.  Create a new file in the `includes/traits/` directory following WordPress naming conventions (e.g., `trait-my-custom.php`).
2.  Define the trait and its methods.

### Example: `trait-my-custom.php`

```php
<?php

namespace WPPB\Traits;

defined( 'ABSPATH' ) || exit;

trait MyCustomTrait {

    /**
     * A custom reusable function.
     * @param string $name
     * @return string
     */
    public function say_hello( string $name ): string {
        return "Hello, " . esc_html($name);
    }
}
```

You can then use this new trait in any class just like the existing ones.

```php
use WPPB\Traits\MyCustomTrait;

class MyExampleClass {
    use MyCustomTrait;

    public function some_method() {
        echo $this->say_hello('World');
    }
}
```

