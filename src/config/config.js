/**
 * Base configuration for webpack
 * Provides essential configuration values
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
    // Environment mode
    mode: env.isProduction ? 'production' : 'development',
    
    // Path configuration (using path utility)
    paths: pathUtils.paths,
    
    // File naming pattern for cache busting
    fileName: env.isProduction ? '[name].[contenthash]' : '[name]',
    
    // Public path for assets
    publicPath: publicPath,
};

export default config;
