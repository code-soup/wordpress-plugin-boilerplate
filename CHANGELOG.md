# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased]

## [0.0.4] - 2026-11-16

### Fixed

- Register admin and frontend asset hooks before `Hooker::run()` so default bundles enqueue.
- `Assets::asset_exists()` now resolves manifest paths correctly.
- Show compatibility admin notices when requirements fail (`Hooker::run()` on early exit).
- Use prefixed plugin file constant and `index.php` as the main plugin file path for `Plugin::instance()`.
- Setup replaces activation/deactivation hook prefixes; activator/deactivator use `__PLUGIN_PREFIX__`.
- Reject empty PHP namespace during setup (validate before shell escaping).
- Webpack passes `isWatching` into style loaders for `webpack serve`.
- Enable source maps for the Sass/`resolve-url-loader` chain in production builds.
- Align `.env.local.example` with `DEV_PROXY_URL` / `DEV_PROXY_PORT` used by webpack.
- Hooker accepts closures and invokable objects as callbacks.
- Optional MySQL/MariaDB version requirement check when `MIN_MYSQL_VERSION` is set.
- Safer `sed` replacements and targeted `wppb`/`wppb-` prefix rewrites in setup.
- `composer.json` setup updates via Node script (namespace-safe).

### Changed

- Removed unused `vlucas/phpdotenv` Composer dependency.
- README and configuration docs: correct composer script names, project structure link, env example, `dos2unix` note.
- Commented default `flush_rewrite_rules()` until rewrites are registered; documented `{prefix}_activate` hooks.
- `ServiceProviderInterface` includes `boot()`; consistent `declare(strict_types=1)` and `ABSPATH` guards in includes.
- JS entries wire `hmr-helper` for webpack dev server HMR; removed unused entry prepending.
- Default SCSS emits a minimal `.wppb-root` rule so production CSS is non-empty.
- `Container::has()` mirrors auto-resolution for class names.
- Admin and frontend `Init` use constructor injection; providers register factory closures.
- Hooker policy documented: Hooker by default, explicit exceptions when it cannot work.
- Uninstaller template includes commented cleanup checklist; activation docs reference it.
- Version bumped to 0.0.4 in `index.php` and `package.json`.

### Added

- `setup/lib-replace.sh`, `setup/update-composer-json.mjs`, optional `WP_PUBLIC_PATH` setup prompt.
- `readme.txt.example`; autoloader map documentation.
- Hooker lifecycle and provider boot documentation; `readme.txt.example`.

### Removed

- Cloud agent review export from repository root.

## [0.0.3] - 2026-07-16

### Security

- Resolved 13 npm security vulnerabilities

### Changed

- Updated npm packages
- Updated composer packages
- Converted config files to ES module syntax
- Replaced eslint-plugin-import with eslint-plugin-import-x

### Added

- Vendor prefixing infrastructure
- Environment configuration template
- Dynamic namespace detection in autoloader
- Auto-detection of license URIs in setup script

### Removed

- @babel/plugin-transform-class-properties
- .stylelintrc.json

### Fixed

- Missing `load_textdomain()` method in Plugin class
- Version synchronization between `package.json` and `index.php`
- Autoloader namespace handling
- Asset loader manifest.json usage

## 2026-02-28

- NPM dependency upgrades
- Composer dependency upgrades
- Switched to WPCS with custom autoloader
- Added agent-skills

## 2024-12-24

- NPM dependency upgrades

## 2024-06-26

### Added

- Uninstaller class
- Service Provider pattern documentation with examples

### Removed

- Lifecycle Class
- Core Provider
- Documentation Trait

### Changed

- Webpack HMR update
- Container optimizations and bug fixes
- Plugin class bug fixes
- Various minor bug fixes
- AI rules update
- Documentation updates
- Setup script updates

## 2024-06-22

### Changed

- Improved and simplified webpack build script
- Container optimizations and bug fixes
- Improved AI rules
- Bug fixes
- Documentation updates
- Dependency upgrades

## 2025-06-07

### Added

- **Dependency Injection Container**: Implemented a DI container to manage service registration and resolution.
- **Service Provider Architecture**: Added a service provider system to organize and bootstrap plugin functionality.

### Changed

- **PHP Architecture Overhaul**: The plugin's core initialization is now driven by the DI container and service providers, resolving numerous stability issues and memory leaks.
- **Webpack Configuration Rework**: The webpack configuration (`src/config/`) has been refactored to use ES Modules, and performance has been optimized by caching environment checks and improving file-watching rules.

## 2022-04-30

### Update

- Dependencies update to latest versions
- Minimum node version required is now 16.16.0
- Automatic image optimization removed

## 2022-07-23

### Update

- Renamed `npm start` to `npm run dev`
- Dependencies update to latest versions
- Minimum node version required is now 16.16.0

## 2021-10-10

### Added

- Resolver alias for @images, @fonts and @icons

## 2021-09-22

### Added

- This CHANGELOG file to hopefully serve as an evolving example of a
  standardized open source project CHANGELOG.
- Support for Vue.js components, does not include VueX (Documentation to follow)
- Minimum node version is now 14.x
