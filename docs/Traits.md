# Traits

Traits are a mechanism for code reuse in single-inheritance languages like PHP. In this boilerplate, they are used to group related, reusable methods that can be easily added to different classes.

This approach is preferred over maintaining a large, procedural `helpers.php` file, as it keeps code organized and contextually relevant.

## Available Traits

-   `HelpersTrait`: Provides helper methods for accessing plugin configuration values (like name, version, path) and performing common string manipulations.
-   `LoggingTrait`: Contains methods for logging debug information.
-   `ValidationTrait`: Provides methods for data validation.
-   `DocumentationTrait`: Helper methods for generating documentation.

## Using an Existing Trait

To use a trait, include the `use` statement inside your class definition. This will make the trait's methods available on the class instance as if they were defined directly in that class.

**Important Note:** The `HelpersTrait` depends on a `$this->config` property being available on the class that uses it. The main `Plugin` class has this property, making it a primary user of this trait.

### Example: Using `HelpersTrait` in a new class

If you want to use the `HelpersTrait` in a class other than `Plugin`, you must ensure that class has access to the plugin's configuration array.

```php
<?php

namespace WPPB\Admin;

use WPPB\Core\Plugin;
use WPPB\Traits\HelpersTrait;

class MyAdminPage {

    // 1. Declare that you are using the trait.
    use HelpersTrait;

    /**
     * The plugin's configuration array.
     * @var array
     */
    protected array $config;

    /**
     * Constructor.
     */
    public function __construct() {
        // 2. Get the main plugin instance to access its config.
        $plugin = Plugin::instance();
        $this->config = $plugin->config;

        // 3. Now you can use methods from the HelpersTrait.
        $this->display_plugin_version();
    }

    /**
     * Example method that uses a trait function.
     */
    public function display_plugin_version(): void {
        // 'get_version()' is a method from HelpersTrait.
        $version = $this->get_version();
        echo esc_html( "The plugin version is: " . $version );
    }
}
```

## Creating a New Trait

You can create your own traits to group functions for a specific purpose.

1.  Create a new file in the `includes/traits/` directory (e.g., `MyCustomTrait.php`).
2.  Define the trait and its methods.

### Example: `MyCustomTrait.php`

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

```php
namespace MyBookShelf\Library;

class Book {

	// Declare which trait you want to use
	use \MyBookShelf\Traits\HelpersTrait

	public function __construct() {

		/**
		 * I can call 'get_plugin_dir_path' from HelpersTrait just as it is defined 
		 */
		$path = $this->get_plugin_dir_path();
	}
}
```

