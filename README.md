## WordPress Plugin Boilerplate
Updated verision of [WordPress Plugin Boilerplate](https://github.com/DevinVinson/WordPress-Plugin-Boilerplate).
Includes a Webpack build script and webpack-dev-server for local development.
Integrates PHP namespacing and PSR-4 autoloader for better experience.


## Issues
Please use [Github issues](https://github.com/code-soup/wordpress-plugin-boilerplate/issues) to submit any bugs you may find.


## Documentation
Plugin documentation with instalation instruction and best practices can be found at [wiki page](https://github.com/code-soup/wordpress-plugin-boilerplate/wiki).


## Features
* Sass for stylesheets
* Stylelint
* ES6 with Babel for JavaScript
* ESLint
* [Vue.js](https://vuejs.org/) support (without VueX)
* [Webpack 5](https://webpack.github.io) build script for frontend and wp-admin assets
* [Webpack dev server](https://github.com/webpack/webpack-dev-server) with live reloading and HMR
* PSR-4 Autoloader
* [SVG Spritemap by cascornelissen](https://github.com/cascornelissen/svg-spritemap-webpack-plugin)

## Requirements
Make sure all dependencies have been installed before moving on:
* [WordPress](https://wordpress.org/) >= 5.0
* [PHP](http://php.net/manual/en/install.php) >= 7.3
* [Composer](https://getcomposer.org/download/)
* [Node.js](http://nodejs.org/) >= 16.16
* [Yarn](https://yarnpkg.com/en/docs/install)

## Coding Standards

- `wpcs` : analyze code against the WordPress coding standards with PHP_CodeSniffer.
- `cbf` : fix coding standards warnings/errors automatically with PHP Code Beautifier.
- `lint` : lint PHP files against parse errors.

To check a file against the WordPress coding standards or to automatically fix coding standards, simply specify the file's location:

- `wpcs includes/class-init.php`
- `cbf includes/class-init.php`

#### License
This project is licensed under the [GPL license](http://www.gnu.org/licenses/gpl-3.0.txt).
