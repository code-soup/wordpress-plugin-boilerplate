/**
 * Base configuration for webpack
 * Provides essential configuration values
 */

const path = require('path');
const dotenv = require('dotenv');
const pathUtils = require('./util/paths');
const env = require('./util/env');

// Plugin directory name detection for WordPress
const pluginDirName = path.basename(path.join(__dirname, '../..'));

// Load environment variables from .env.local file
dotenv.config({
    path: pathUtils.fromRoot('.env.local'),
});

/**
 * Auto-set public path or read from .env
 */
const publicPath =
    'undefined' === typeof process.env.WP_PUBLIC_PATH
        ? `${process.env.WP_CONTENT_PATH}/${pluginDirName}/dist/`
        : process.env.WP_PUBLIC_PATH;

/**
 * Base configuration object
 */
module.exports = {
    // Environment mode
    mode: env.isProduction ? 'production' : 'development',
    
    // Path configuration (using path utility)
    paths: pathUtils.paths,
    
    // File naming pattern for cache busting
    fileName: env.isProduction ? "[name].[contenthash]" : "[name]",
    
    // Public path for assets
    publicPath: publicPath,
};
