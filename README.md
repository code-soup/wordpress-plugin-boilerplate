# WordPress Plugin Boilerplate

A boilerplate for creating WordPress plugins, featuring a pre-configured build process and a structured PHP foundation.

[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)
[![Build Status](https://img.shields.io/badge/build-passing-brightgreen.svg)](https://github.com/code-soup/wordpress-plugin-boilerplate)
[![PHP Version: >=8.1](https://img.shields.io/badge/php->=8.1-8892BF.svg)](https://www.php.net/)
[![Node Version: >=22.0](https://img.shields.io/badge/node->=22.0-339933.svg)](https://nodejs.org/)

This boilerplate provides a structured starting point for plugin development, including a Webpack 5 configuration, a Dependency Injection Container, and a modular PHP architecture.

## Features

-   **PHP Foundation**: Built for PHP 8.1+, using namespaces and PSR-4 autoloading.
-   **Dependency Injection**: Includes a Dependency Injection Container for managing services and dependencies.
-   **Service Provider Architecture**: Provides a structure for organizing plugin features into modular Service Providers.
-   **Webpack 5**: A pre-configured Webpack setup for asset bundling.
    -   **Hot Module Replacement (HMR)**: A development server with live-reloading.
    -   **Code Splitting**: Automatic chunking of vendor libraries and common modules.
    -   **Asset Minification**: Minified assets for production builds.
-   **Frontend Asset Pipeline**:
    -   **ESNext & Babel**: JavaScript transpilation for browser compatibility.
    -   **Sass**: A preprocessor for writing structured CSS.
    -   **PostCSS**: Vendor prefixing with `postcss-preset-env`.
    -   **SVG Sprites**: Automatic SVG spritemap generation.
-   **Code Quality & Linting**:
    -   **ESLint**: For enforcing JavaScript coding standards.
    -   **Stylelint**: For enforcing SCSS coding standards.
    -   **PHP_CodeSniffer**: Includes WordPress Coding Standards (`wpcs`) for PHP.

## Requirements

Before you begin, please ensure you have the following installed:

-   PHP >= 8.1
-   Node.js >= 22.0
-   Yarn (v1)
-   Composer

## Quick Start

1.  **Clone the Repository**:
    ```bash
    git clone https://github.com/code-soup/wordpress-plugin-boilerplate.git my-awesome-plugin
    cd my-awesome-plugin
    ```

2.  **Run the Setup Script**:
    This script will ask for your plugin's details and configure the boilerplate files for you.
    ```bash
    # First, make the script executable
    chmod u+x setup.sh

    # Then, run the script
    sh setup.sh
    ```

3.  **Install Dependencies**:
    Once the setup is complete, install the necessary PHP and Node.js dependencies.
    ```bash
    # Install PHP dependencies
    composer install

    # Install Node.js dependencies
    yarn install
    ```

4.  **Run the Development Server**:
    For live-reloading and automatic recompilation of assets during development.
    ```bash
    yarn dev
    ```

## Available Scripts

This boilerplate comes with a set of pre-configured scripts for development tasks.

### PHP Scripts (via Composer)

-   **`composer lint`**: Lints all PHP files for syntax errors.
-   **`composer wpcs`**: Checks PHP files against the WordPress Coding Standards.
-   **`composer cbf`**: Automatically fixes many `phpcs` errors.

### JavaScript/Asset Scripts (via Yarn/NPM)

-   **`yarn dev`**: Starts the webpack dev server with Hot Module Replacement.
-   **`yarn build`**: Compiles and optimizes all assets for a production environment.
-   **`yarn build:dev`**: Compiles assets for development without optimization.
-   **`yarn lint`**: Runs both the script and style linters.
    -   **`yarn lint:scripts`**: Lints JavaScript files with ESLint.
    -   **`yarn lint:styles`**: Lints SCSS files with Stylelint.
-   **`yarn clean`**: Deletes the `dist` folder and the webpack cache.

## Project Structure

-   `includes/`: Contains all the PHP source code for the plugin.
    -   `core/`: The core bootstrap and DI container logic.
    -   `admin/`: Code specific to the WordPress admin area.
    -   `frontend/`: Code specific to the public-facing parts of the site.
    -   `providers/`: Service providers for registering plugin features.
-   `src/`: Contains all the raw, un-compiled frontend assets.
    -   `scripts/`: JavaScript files.
    -   `styles/`: SCSS files.
    -   `images/`, `icons/`, `fonts/`: Other static assets.
    -   `config/`: The entire Webpack configuration.
-   `dist/`: The output directory for all compiled assets. This directory is automatically generated.
-   `languages/`: Contains translation files (`.pot`, `.po`, `.mo`).

## Documentation

For more detailed information on specific topics, please refer to the documentation in the `/docs` directory.

-   **[About](./docs/About.md)**: A general overview of the project's purpose and features.
-   **[Installation](./docs/Installation.md)**: Step-by-step guide to setting up the boilerplate.
-   **[Project Structure](./docs/ProjectSructure.md)**: An explanation of the project's directory layout.
-   **[Configuration](./docs/Configuration.md)**: How to configure the plugin and add new webpack entry points.
-   **[Using the Hooker](./docs/Hooker.md)**: How to use the `Hooker` service to add actions and filters.
-   **[Using Traits](./docs/Traits.md)**: How to use and create reusable traits.
-   **[Plugin Activation/Deactivation](./docs/Activation.md)**: How to run code on the plugin activation and deactivation.
-   **[SCSS - General Usage](./docs/SCSS-General.md)**: An overview of the Sass setup, including path resolution and available mixins.
-   **[SCSS - Custom Fonts](./docs/SCSS-Fonts.md)**: How to add and use custom self-hosted fonts.
-   **[SCSS - SVG Sprites](./docs/SCSS-Spritemap.md)**: How to use the automated SVG spritemap generator.

## Issues

If you encounter a bug or have a feature request, please [submit an issue on GitHub](https://github.com/code-soup/wordpress-plugin-boilerplate/issues). When creating an issue, provide a clear, descriptive title and include as much detail as possible to help us understand and reproduce the problem.

## License

This project is licensed under the [GPLv3 License](https://www.gnu.org/licenses/gpl-3.0.txt).
