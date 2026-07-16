# Changelog

All notable changes to this project will be documented in this file.

## [0.0.3] - 2026-06-29

### Security

- Resolved 13 npm security vulnerabilities (4 critical, 5 high, 4 moderate)
- Upgraded webpack-dev-server from 1.16.5 to 5.2.4
- Added uuid package override to force version >=11.1.1

### Changed

- Replaced eslint-plugin-import with eslint-plugin-import-x for ESLint 10 compatibility
- Updated ESLint configuration to use import-x namespace
- Added @eslint/js dependency for flat config support
- Moved eslint-plugin-import-x to devDependencies

### Added

- Vendor prefixing infrastructure for preventing dependency conflicts
    - Created `scoper.inc.php.example` template with WordPress-safe configuration
    - Added comprehensive vendor prefixing documentation (`docs/016-Vendor-Prefixing.md`)
    - Updated `.gitignore` to exclude scoped vendor directory
- Environment configuration template (`.env.local.example`)
- Dynamic namespace detection in autoloader
    - Autoloader now uses `__NAMESPACE__` instead of hardcoded namespace
    - Ensures compatibility after setup script changes namespace
- Auto-detection of license URIs in setup script
    - Supports GPL-3.0+, GPL-2.0+, MIT, Apache-2.0, BSD licenses
    - Users can leave License URI blank for automatic URL assignment

### Fixed

- **Critical:** Added missing `load_textdomain()` method in Plugin class
    - Plugins created from boilerplate were failing on activation
    - Method uses `__PLUGIN_TEXTDOMAIN__` placeholder replaced by setup script
- Version synchronization between `package.json` and `index.php`
- Autoloader fragility when setup script changes namespace
- CHANGELOG version history cleaned up to reflect actual git history
- Fixed asset loader to always use manifest.json

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
