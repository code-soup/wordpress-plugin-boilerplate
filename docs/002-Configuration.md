# Configuration

## Environment Variables

Environment variables are saved in `.env.local` during setup. This file is not committed to version control and can be used to configure the local development environment.

Example `.env.local`:

## Adding entry points

Webpack entry points are defined in the `src/config/entry.js` file.

To add a new entry point, add a new key-value pair to the exported object.
- The **key** is the output file name (without extension).
- The **value** is an array of paths to the source files, relative to the `src/` directory.

Example: To add a new `custom` entry point that compiles `custom.js` and `custom.scss`, edit `src/config/entry.js` as follows:

```javascript
export default {
	'admin-common': ['./scripts/admin.js', './styles/admin.scss'],
	common: ['./scripts/main.js', './styles/main.scss'],
    // New entry point
    custom: ['./scripts/custom.js', './styles/custom.scss'],
};
```
This configuration will generate `dist/scripts/custom.js` and `dist/styles/custom.css`.

## Build commands

The following scripts are available for building assets:

-   `npm run dev`: Starts the webpack development server with live reloading.
-   `npm run build`: Creates a production build with optimized and minified assets.
-   `npm run build:dev`: Creates a development build without optimizations.

### Additional commands

**Code Quality**
-   `npm run lint`: Runs all JavaScript and style linters.
-   `npm run lint:scripts`: Lints JavaScript files with ESLint.
-   `npm run lint:styles`: Lints SCSS files with Stylelint.
-   `composer lint`: Lints PHP files for syntax errors.
-   `composer wpcs`: Checks PHP files against WordPress Coding Standards.
-   `composer cbf`: Attempts to automatically fix `phpcs` errors.

**Maintenance**
-   `npm run clean`: Removes the `dist` directory and the webpack cache.
-   `npm run reinit`: Removes `dist` and `node_modules`, then re-installs dependencies.
