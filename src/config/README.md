# Webpack Configuration Structure

This directory contains the webpack configuration for building the WordPress plugin. The configuration has been designed with a modular approach to improve maintainability, readability, and reusability.

## Directory Structure

```
src/config/
├── config.js            # Base configuration values
├── config.webpack.js    # Main webpack configuration entry point
├── factory.js           # Configuration factory pattern
├── util/                # Utility functions
│   ├── dynamic-entry.js # Dynamic entry point handling
│   ├── env.js           # Environment detection
│   ├── paths.js         # Path utilities
│   └── plugins.js       # Plugin utilities
└── webpack/             # Webpack configuration modules
    ├── config.module.js       # Module rules configuration
    ├── config.optimization.js # Optimization configuration
    ├── config.plugins.js      # Plugins configuration
    ├── config.watch.js        # Development server configuration
    └── loaders/               # Individual loader configurations
        ├── assets.js          # Asset loaders (images, fonts, etc.)
        ├── pre.js             # Pre-loaders (import-glob)
        ├── scripts.js         # JavaScript loaders
        └── styles.js          # Stylesheet loaders
```

## Design Patterns

The configuration follows several design patterns:

1. **Factory Pattern**: The `factory.js` file acts as a factory for creating webpack configurations. This centralizes the configuration creation and allows for overrides and extensions.

2. **Modular Configuration**: Each aspect of the webpack configuration is separated into its own module (plugins, optimization, module rules, etc.).

3. **Utility-Based Design**: Common functionality is extracted into utilities (environment detection, path handling, plugin loading).

4. **Conditional Logic**: Configuration options are applied conditionally based on the environment (production/development) and build flags.

## Key Features

- **Environment Detection**: The `env.js` utility provides a centralized way to detect various environments and operation modes.

- **Path Handling**: The `paths.js` utility provides consistent path resolution throughout the configuration.

- **Dynamic Entry Points**: The `dynamic-entry.js` utility allows for dynamically loading entry points from the `src/entry` directory.

- **Conditional Plugin Loading**: The `plugins.js` utility provides a clean way to conditionally load plugins based on various criteria.

- **Loader Separation**: Each type of loader is separated into its own file for better organization.

## Usage

The main entry point is `config.webpack.js`, which uses the factory pattern to create the webpack configuration. To customize the configuration, you can:

1. Modify the base configuration in `config.js`
2. Override specific aspects in the appropriate module files
3. Use the factory directly with custom options:

```javascript
const createWebpackConfig = require('./src/config/factory');

const customConfig = createWebpackConfig({
  config: {/* custom base config */},
  overrides: {/* webpack config overrides */}
});
```

## Environment Variables

The configuration uses the following environment variables:

- `NODE_ENV`: Production environment when set to `production`
- `WP_PUBLIC_PATH`: Public path for assets
- `WP_CONTENT_PATH`: WordPress content directory
- `DEV_PROXY_PORT`: Development server port
- `WP_DEV_URL`: WordPress development URL for proxy
- `ANALYZE`: Set to `true` to enable bundle analysis 