## WordPress Plugin Boilerplate
This is simplified fork of [WordPress Plugin Boilerplate](https://github.com/DevinVinson/WordPress-Plugin-Boilerplate).
Includes a Webpack build script and webpack-dev-server for local development.
Integrates PHP namespacing and PSR-4 autoloader for better experience.

Did I mention this is still in Beta phase?
Should work but might have a bug or two, and no docs yet.
Please use issues to submit any bugs.


## Features
* Sass for stylesheets
* ES6 with Babel for JavaScript
* [Webpack](https://webpack.github.io) build script for frontend and wp-admin assets
* [Webpack dev server](https://github.com/webpack/webpack-dev-server) that provides live reloading during development
* PSR-4 Autoloader
* Stylelint
* ESLint
* [SVG Spritemap by cascornelissen](https://github.com/cascornelissen/svg-spritemap-webpack-plugin)


## Requirements
Make sure all dependencies have been installed before moving on:
* [WordPress](https://wordpress.org/) >= 4.7
* [PHP](http://php.net/manual/en/install.php) >= 7.2
* [Composer](https://getcomposer.org/download/)
* [Node.js](http://nodejs.org/) >= 12.6.x
* [Yarn](https://yarnpkg.com/en/docs/install)



## Local development setup
1. Fork or clone repository in your local dir, eg:
```shell
~/wp-content/plugins/your-plugin-name/
```
Run `git clone git@github.com:code-soup/wordpress-plugin-boilerplate.git .` in that local dir via terminal.
All the following comands need to be run in this folder via terminal.

2. Remove template git repo & start fresh (one command at a time)\
`rm -rf .git`\
`git init`\
`git add .`\
`git commit -am 'init'`

3. Update `wppb` namespace in `composer.json`, `run.php` and `includes` folder files.

4. Update plugin details (PLUGIN_NAME, PLUGIN_VERSION and PLUGIN_TEXT_DOMAIN) in `run.php`.

5. Update plugin details (Name, Description, etc..) in `index.php` file header.

6. Install PHP dependencies\
`composer install`

7. Install node packages\
`yarn`

8. Make a copy of `/src/config.js`, rename it to `/src/config-local.js` and update paths to your local environment.

9. Create initial build\
`yarn build`

10. Go to `wp-admin/plugins.php` and activate your plugin.


## Configure development settings
This is done in config-local.js
* `entry` - setup root files of scripts and styles for frontend & backend
* `openBrowserOnWatch` — Open a browser tab with local devUrl on `yarn start` compile, set to `true` or `false`
* `showErrorsInBrowser` — Webpack errors will be shown on frontend if set to `true`, otherwise errors will only be shown in CLI
* `useSSLinDev` — If your site uses uses SSL (https) set this to `true`
* `publicPath` — file path to the plugin
* `devUrl` — `localhost` or custom domain name for local development
* `proxyPort` — Set a port to use on `yarn start`
* `watch` — folders/files to include in live reload


### Build commands
* `yarn start` — Start your development process, this will compile and live reload your browser or inject css when possible while in development
* `yarn build` — Compile and optimize the files
* `yarn build:prod` — Compile and optimize assets for production (minify css/js and run image optimization)


#### Additional commands
* `yarn clean` — Remove your `dist` folder
* `yarn reinit` — Remove your `dist` and `node_modules` folder and reinstall node dependencies
* `yarn lint` — Run ESLint/Stylelint against your source files and build scripts
* `yarn lint:scripts` — Run ESLint against your source files and build scripts
* `yarn lint:styles` — Run Stylelint against your source files


#### License
This project is licensed under the [GPL license](http://www.gnu.org/licenses/gpl-3.0.txt).