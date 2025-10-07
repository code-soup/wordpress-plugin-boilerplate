# Changelog

All notable changes to this project will be documented in this file.

## [2.0.2] - 2024-06-26

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


## [2.0.1] - 2024-06-22

### Changed

- Improved and simplified webpack build script
- Container optimizations and bug fixes
- Improved AI rules
- Bug fixes
- Documentation updates
- Dependency upgrades

## [2.0.0] - 2025-06-07

### Added

- **Dependency Injection Container**: Implemented a DI container to manage service registration and resolution.
- **Service Provider Architecture**: Added a service provider system to organize and bootstrap plugin functionality.

### Changed

- **PHP Architecture Overhaul**: The plugin's core initialization is now driven by the DI container and service providers, resolving numerous stability issues and memory leaks.
- **Webpack Configuration Rework**: The webpack configuration (`src/config/`) has been refactored to use ES Modules, and performance has been optimized by caching environment checks and improving file-watching rules.

## [1.0.3] - 2022-04-30

### Update

- Dependencies update to latest versions
- Minimum node version required is now 16.16.0
- Automatic image optimization removed

## [1.0.2] - 2022-07-23

### Update

- Renamed `npm start` to `npm run dev`
- Dependencies update to latest versions
- Minimum node version required is now 16.16.0

## [1.0.2] - 2021-10-10

### Added

- Resolver alias for @images, @fonts and @icons

## [1.0.1] - 2021-09-22

### Added

- This CHANGELOG file to hopefully serve as an evolving example of a
  standardized open source project CHANGELOG.
- Support for Vue.js components, does not include VueX (Documentation to follow)
- Minimum node version is now 14.x
