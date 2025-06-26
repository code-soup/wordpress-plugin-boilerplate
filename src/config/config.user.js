/**
 * User-configurable settings for webpack.
 * Includes base configuration and entry points.
 */

import path from "path";
import dotenv from "dotenv";
import * as pathUtils from "./util/paths.js";

// Plugin directory name detection for WordPress
const pluginDirName = path.basename(path.join(pathUtils.paths.config, "../.."));

// Load environment variables from .env.local file
dotenv.config({
	path: pathUtils.fromRoot(".env.local"),
});

// ---------------------------------------------------------------------------
// Public path logic
// ---------------------------------------------------------------------------
// 1. If you pass WP_PUBLIC_PATH it always wins (CI / production builds).
// 2. While running the local dev-server (webpack-serve / yarn dev) we want a
//    hard-coded path that points to the HMR server on localhost:8080 so the
//    runtime can fetch hot-update files without any extra env vars.
// 3. In every other case fall back to the default WP plugin path.

// Public path:  use env-var if present, otherwise default to WP plugin dir
const publicPath =
	process.env.WP_PUBLIC_PATH ??
	`${process.env.WP_CONTENT_PATH || '/wp-content/plugins'}/${pluginDirName}/dist/`;

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
		"admin-common": ["./scripts/admin.js", "./styles/admin.scss"],
		"ziploy-common": ["./scripts/main.js", "./styles/main.scss"],
	},

	// Path configuration (using path utility)
	paths: pathUtils.paths,

	// Public path for assets, detected automatically or from .env
	publicPath: publicPath,
};

export default config;
