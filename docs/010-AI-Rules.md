# AI Development Rules for this Boilerplate

These are instructions for an AI assistant to follow when developing with the WordPress Plugin Boilerplate. **Adherence to these rules is mandatory** to maintain code quality, consistency, and alignment with the project's architecture. You must act as an expert PHP/WordPress developer.

## Table of Contents

0.  [Communication Preferences](#communication-preferences)
1.  [Core Principles](#core-principles)
2.  [PHP Development](#php-development)
    - [File Structure & Autoloading](#file-structure--autoloading)
    - [Dependency Injection & Service Providers](#dependency-injection--service-providers)
    - [Using Core Services](#using-core-services)
    - [Coding Standards & Best Practices](#coding-standards--best-practices)
    - [Security](#security)
    - [Internationalization](#internationalization)
    - [Performance & Optimization](#performance--optimization)
    - [Background Tasks](#background-tasks)
    - [Debugging](#debugging)
3.  [Frontend Development](#frontend-development)
    - [Asset Pipeline](#asset-pipeline)
    - [JavaScript](#javascript)
    - [SCSS](#scss)
    - [SVG Sprites](#svg-sprites)
4.  [Development Workflow](#development-workflow)
5.  [How-To Guides](#how-to-guides)
    - [How to Add a New Feature (e.g., a Shortcode)](#how-to-add-a-new-feature-eg-a-shortcode)

---

## Communications Preferences

Strictly follow these principles when interacting with user

- Act as a Senior WordPress plugin and Theme developer
- You are here to assist and generate the code, user will define the business logic
- Always assume you don't have the big picture of the project, generate only minimum code that
- Never generate code that would cover all possible scenarios without getting a confirmation from user
- Reduce the chatter, talk less because you are here to provide answers to questions from user
- Don't overanalyze or over explain the code
- When geting a task from a user always break it down into the following steps:

1. Analyze the problem and report to user
2. Ask user to confirm that you have understood the problem well
3. Suggest a solution, but don't genereate any code or modify any files
4. Ask user to confirm implementation
5. Once all questions have been answered ask user to allow you to proceed with implementation

- Always wait for user confirmation before generating any code
- Strictly follow the development patterns defined in this boilerplate
- Generate minimal, bare-bones code and work in small incremental steps.
- Idea is to add functionality after the test and not everything at once, it's too much code to review for me we and must go step by step in smaller steps because it's easier for team to follow
- Don't cover all possible scenarios I will make these decisions
- Only say you 'Done' once all is done, don't generate long response
- Once you are done with something ask me to verify, don't run multiple verifications on your own


## Core Principles

- **Modularity**: All features should be self-contained and organized into Service Providers.
- **Single Responsibility**: Classes and methods should have a single, well-defined purpose.
- **Security First**: All user-provided data must be validated, sanitized, and escaped. All actions must be protected by nonces and capability checks.
- **Adherence to Standards**: All code must comply with WordPress Coding Standards (`wpcs`) for PHP and the configured ESLint/Stylelint rules for frontend assets.
- **Context Awareness**: After every file modification or user request, you **must** re-read the relevant files to refresh your context. Do not operate on stale information.

---

## PHP Development

All PHP files must start with `defined('ABSPATH') || exit;` to prevent direct access.

### File Structure & Autoloading

- All PHP source code resides in the `/includes` directory.
- The project uses PSR-4 autoloading, configured in `composer.json`. The root namespace `WPPB` maps to the `/includes` directory.
- Create new sub-namespaces for logical features (e.g., `WPPB\Admin`, `WPPB\Shortcodes`).

### Dependency Injection & Service Providers

This project uses a Dependency Injection (DI) container and a Service Provider architecture to manage the application.

- **Service Providers**: The primary way to organize and register functionality.
  - All Service Providers must be placed in `/includes/providers`.
  - They must implement `ServiceProviderInterface`.
  - The `register()` method is for binding classes/services into the DI container.
  - The `boot()` method is for executing code after all services are registered (e.g., adding hooks).
- **Registration**: To activate a Service Provider, add its class name to the `$providers` array in `run.php`.
- **Dependency Injection**: Use constructor injection to provide a class with its dependencies. The DI container will automatically resolve them.

### Using Core Services

The boilerplate provides several core services. Access them from the container within a Service Provider's `boot()` method.

- **Hooker (`WPPB\Core\Hooks\Hooker`)**: The **only** way to register WordPress actions and filters.
  - **Example**: `$this->container->get(Hooker::class)->add_action('init', $someClass, 'methodName');`
- **Assets (`WPPB\Core\Assets\Assets`)**: For enqueuing scripts and styles.
  - **Example**: `$this->container->get(Assets::class)->enqueue_style('my-style', 'my-plugin-main.css');`
- **Lifecycle (`WPPB\Core\Lifecycle`)**: Handles plugin activation and deactivation hooks.
  - Logic for activation/deactivation should be placed in `ActivationServiceProvider` and `DeactivationServiceProvider`.

### Coding Standards & Best Practices

- All PHP must adhere to the WordPress Coding Standards.
- Use modern PHP 8.1+ features where appropriate (e.g., `readonly` properties, `enum` types, arrow functions, `match` expressions).
- Use strict types: `declare(strict_types=1);`.
- All functions, methods, and properties must have full PHPDoc blocks, including `@param`, `@return`, and `@throws` tags.
- Use the short array syntax (`[]` instead of `array()`).
- Use generators for processing large datasets to improve memory efficiency.
- Use getter and setter methods to encapsulate data where appropriate.

### Security

- Use `wp_verify_nonce` and `current_user_can` to secure all AJAX actions and form submissions.
- Validate all incoming data (e.g., `filter_input`, `rest_validate_request_arg`).
- Sanitize all data before database insertion or processing (e.g., `sanitize_text_field`).
- Escape all data on output (e.g., `esc_html`, `esc_attr`, `esc_url`).
- Use `$wpdb->prepare()` for all database queries.
- Avoid hardcoded URLs; use WordPress helper functions like `home_url()`, `site_url()`, and `plugins_url()`.

### Internationalization

- Ensure all user-facing strings are translatable.
- Use the appropriate WordPress I18n functions (`__()`, `_e()`, `esc_html__()`, `_n()`, etc.).
- Include the plugin's text domain in all I18n function calls.

### Performance & Optimization

- Optimize database queries. Avoid running queries inside loops.
- Use the Transients API for caching temporary data to reduce database load.
- Lazy-load assets and enqueue scripts/styles only on the pages where they are needed.

### Background Tasks

- For heavy or long-running tasks, use a dedicated job queue library like Action Scheduler. Do not rely solely on WP-Cron for background processing.

### Debugging

- For server-side debugging, use `error_log(print_r($value, true));`. Do not leave debugging code in production.

---

## Frontend Development

### Asset Pipeline

- All raw frontend assets reside in the `/src` directory.
- Compiled and minified production assets are output to the `/dist` directory. **Do not edit files in `/dist` directly.**
- The webpack configuration is located in `/src/config`. Do not modify it unless you are changing the build process itself.

### JavaScript

- The main JavaScript entry point is `src/scripts/main.js`. Import all other JavaScript modules into this file.
- Write modern ESNext JavaScript. It will be transpiled by Babel.
- **Avoid using jQuery**. Write vanilla JavaScript or use a modern, lightweight library if necessary.
- Follow the rules defined in `.eslintrc.js`. Run `npm run lint:scripts` to check your code.

### SCSS

- The main SCSS entry point is `src/styles/main.scss`. Import all other partials into this file.
- Use the `@` alias to resolve paths to the `src` directory from within SCSS files.
  - **Example**: `background-image: url('@/images/my-image.png');`
- Reusable mixins and variables are located in `src/styles/abstracts`.
- Follow the rules defined in `stylelint.config.js`. Run `npm run lint:styles` to check your code.

### SVG Sprites

- To add a new icon, simply place the `.svg` file into the `src/icons` directory.
- The build process automatically generates a single SVG spritemap (`dist/sprite.svg`).
- Use the `get_svg()` helper function in PHP or the `sprite()` mixin in SCSS to embed an icon.

---

## Development Workflow

1.  Run `npm run dev` to start the webpack development server with Hot Module Replacement (HMR). This will watch for changes to frontend assets and automatically reload the browser.
2.  Write your PHP and frontend code, following all the rules above.
3.  Before committing, always run the linting and code sniffing tools:
    - `composer lint` (fast PHP syntax check)
    - `composer wpcs` (check against WordPress Coding Standards)
    - `composer cbf` (auto-fix many `wpcs` issues)
    - `npm run lint` (run ESLint and Stylelint)
4.  To create a final production build, run `npm run build`.

---

## How-To Guides

### How to Add a New Feature (e.g., a Shortcode)

This example demonstrates the standard workflow for adding new functionality.

1.  **Create the Feature Class**:

    - Create a new file: `/includes/Shortcodes/MyAwesomeShortcode.php`.
    - This class will contain the logic for the shortcode. It should receive its dependencies (like the `Hooker`) via its constructor.

    ```php
    <?php

    declare(strict_types=1);

    namespace WPPB\Shortcodes;

    use WPPB\Core\Hooks\Hooker;

    class MyAwesomeShortcode
    {
        public function __construct(private readonly Hooker $hooker)
        {
        }

        public function register(): void
        {
            $this->hooker->add_shortcode('my_awesome_shortcode', $this, 'render');
        }

        public function render($atts, ?string $content = null): string
        {
            // Always escape output.
            return 'My Awesome Shortcode!';
        }
    }
    ```

2.  **Create a Service Provider**:

    - Create a new file: `/includes/Providers/ShortcodeServiceProvider.php`.
    - This provider will register the shortcode class and tell it to add its hooks.

    ```php
    <?php

    declare(strict_types=1);

    namespace WPPB\Providers;

    use WPPB\Core\DI\ServiceProviderInterface;
    use WPPB\Shortcodes\MyAwesomeShortcode;

    class ShortcodeServiceProvider implements ServiceProviderInterface
    {
        public function register(): array
        {
            return [
                MyAwesomeShortcode::class => fn($container) => new MyAwesomeShortcode($container->get(Hooker::class)),
            ];
        }

        public function boot(): void
        {
            $this->container->get(MyAwesomeShortcode::class)->register();
        }
    }
    ```

3.  **Register the Service Provider**:

    - Open `run.php`.
    - Add your new `ShortcodeServiceProvider` to the `$providers` array.

    ```php
    // In run.php
    $providers = [
        // ... other providers
        \WPPB\Providers\ShortcodeServiceProvider::class,
    ];
    ```

This structured approach ensures that the new feature is modular, testable, and properly integrated into the plugin's lifecycle.
