/**
 * User-configurable settings for webpack.
 * Includes base configuration and entry points.
 */

import path from 'path';
import dotenv from 'dotenv';
import * as pathUtils from './util/paths.js';
import * as env from './util/env.js';

// Plugin directory name detection for WordPress
const pluginDirName = path.basename(path.join(pathUtils.paths.config, '../..'));

// Load environment variables from .env.local file
dotenv.config({
	path: pathUtils.fromRoot('.env.local'),
});

/**
 * Auto-set public path or read from .env
 */
const publicPath =
	'undefined' === typeof process.env.WP_PUBLIC_PATH
		? `${process.env.WP_CONTENT_PATH || '/wp-content/plugins'}/${pluginDirName}/dist/`
		: process.env.WP_PUBLIC_PATH;

/**
 * Base configuration object
 */
const config = {
	/**
	 * Webpack entry points.
	 *
	 * This is the primary place to add, remove, or edit main JavaScript and SCSS files
	 * for your project. Each key in the `entry` object represents a bundle.
	 * - `key`: The name of the output file (e.g., 'admin-common' becomes 'admin-common.js').
	 * - `value`: An array of file paths to be included in the bundle.
	 *
	 * @example
	 * entry: {
	 *   'my-bundle': ['./scripts/my-script.js', './styles/my-style.scss'],
	 * }
	 */
	entry: {
		'admin-common': ['./scripts/admin.js', './styles/admin.scss'],
		common: ['./scripts/main.js', './styles/main.scss'],
	},

	// Environment mode: 'production' or 'development'
	mode: env.isProduction ? 'production' : 'development',

	// Path configuration (using path utility)
	paths: pathUtils.paths,

	// File naming pattern for cache busting in production
	fileName: env.isProduction ? '[name].[contenthash]' : '[name]',

	// Public path for assets, detected automatically or from .env
	publicPath: publicPath,
};

export default config;
