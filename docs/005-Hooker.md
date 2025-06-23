# Hooker

The `Hooker` class is a centralized service for adding WordPress actions and filters. It is registered in the Dependency Injection container and can be accessed from any class that has access to the container, such as a service provider or a class instantiated by one.

The primary benefit of using this service is to have a consistent, injectable way to manage WordPress hooks, which is useful for organization and testing.

## Getting the Hooker Service

The `hooker` service is available from the main plugin container. There are two primary ways to access it.

### 1. From a Service Provider

In any service provider that extends `AbstractServiceProvider`, the container is available via `$this->container`.

```php
// In a service provider, e.g., includes/providers/FrontendServiceProvider.php
$hooker = $this->container->get('hooker');
```

### 2. From the Global Plugin Instance

You can get the main plugin instance using the global `plugin()` function, which holds the container. This approach can be used from anywhere.

```php
// Get the main plugin instance
$plugin = \WPPB\plugin();

// Get the hooker service from the container
$hooker = $plugin->container->get('hooker');
```

## Adding Actions

To add a WordPress action, use the `add_action()` method. It accepts the same arguments as the native WordPress `add_action()` function.

The `callable` argument is typically an array containing an object instance and the name of the method to call.

### Example: Adding an action from a class

This example shows how to register a hook from within a class that is managed by the container.

```php
// In some class, e.g., includes/frontend/Display.php
namespace WPPB\Frontend;

use WPPB\Core\Hooker;

class Display {
    private Hooker $hooker;

    public function __construct() {
        $this->hooker = plugin()->get('hooker');
    }

    public function init(): void {
        $this->hooker->add_action(
            'wp_footer',      // The hook name
            $this,
            'render',         // The callable: this object, 'render' method
            10,               // Priority
            1                 // Number of accepted arguments
        );
    }

    public function render(): void {
        echo '<div>My Plugin Content</div>';
    }
}
```

You would then need to register and initialize this `Display` class within a service provider.

```php
// In a service provider's boot() method:
$display = new \WPPB\Frontend\Display();
$display->init();
```

## Adding Filters

Adding filters works exactly the same way as adding actions, but using the `add_filter()` method.

### Example: Adding a filter

```php
// Get the hooker service
$hooker = \WPPB\plugin()->get('hooker');
$my_class = new MyClass();

$hooker->add_filter(
    'the_title',              // The hook name
    $my_class,
    'modify_title',           // The callable
    10,                       // Priority
    2                         // Number of accepted arguments
);
```

### Example: Calling WordPress built in functions

You can also call WordPress default return functions by simply passing them as second argument

```php
$hooker->add_filter([ 'the_title', '__return_false' ]);
```

## Adding Multiple Actions or Filters at Once

The `Hooker` service also provides `add_actions()` and `add_filters()` methods to register multiple hooks in a single call. Each item in the array should be an array of arguments as you would pass to `add_action()` or `add_filter()`.

### Example: Adding multiple actions

```php
// Register multiple actions at once
$hooker->add_actions([
    [ 'wp_enqueue_scripts', $this ],
    [ 'admin_init', $this, 'on_admin_init', 20 ],
]);
```

### Example: Adding multiple filters

```php
// Register multiple filters at once
$hooker->add_filters([
    [ 'the_content', $this, 'filter_content' ],
    [ 'the_title', $this ], // method name matches hook name, so can be omitted
]);
```

### Omitting the method argument

If the method name is the same as the hook name, you can omit the method argument. The `Hooker` service will automatically use the hook name as the method to call on the object. This works for both single and multiple hook registration methods.

```php
// Equivalent to [ 'the_title', $this, 'the_title' ]
$hooker->add_filter([ 'the_title', $this ]);
```
