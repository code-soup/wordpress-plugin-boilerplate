/**
 * Webpack configuration factory
 * Centralizes creation of webpack configurations
 */

import { fileURLToPath } from 'url';
import { merge } from 'webpack-merge';

// Import utilities
import * as env from './util/env.js';
import * as pathUtils from './util/paths.js';
import baseWebpackConfig from './config.js'; // Default import
import entryConfig from './entry.js';
import moduleConfig from './webpack/config.module.js';
import pluginsConfig from './webpack/config.plugins.js';
import optimizationConfig from './webpack/config.optimization.js';
import watchConfig from './webpack/config.watch.js';

const __filename = fileURLToPath(import.meta.url);

/**
 * Creates a webpack configuration based on environment and options
 * 
 * @param {Object} options - Configuration options
 * @return {Object} Webpack configuration object
 */
function createWebpackConfig(options = {}) {
    // Use provided config or the default one
    const config = options.config || baseWebpackConfig;
    
    // Build the base webpack configuration
    const baseConfig = {
        entry: entryConfig,
        context: config.paths.src,
        output: {
            path: config.paths.dist,
            publicPath: config.paths.publicPath,
            filename: `scripts/${config.fileName}.js`,
            clean: true,
            chunkFilename: `scripts/${config.fileName}.[contenthash].chunk.js`,
        },
        mode: config.mode,
        stats: {
            assets: true,
            colors: true,
            logging: 'warn',
            modules: false,
            entrypoints: false,
        },
        cache: {
            type: 'filesystem',
            buildDependencies: {
                config: [__filename],
            },
            cacheDirectory: pathUtils.paths.cache,
            name: `${env.isProduction ? 'prod' : 'dev'}-cache`,
        },
        target: ['web', 'es5'],
        devtool: env.getEnvSpecific('source-map', 'eval-source-map'),
        module: moduleConfig(config, env),
        resolve: {
            modules: [config.paths.src, 'node_modules'],
            extensions: ['.js', '.json'],
            enforceExtension: false,
            alias: {
                '@': config.paths.src,
            },
        },
        externals: {
            jquery: 'jQuery',
        },
        performance: {
            hints: env.getEnvSpecific('warning', false),
            maxEntrypointSize: 1000000,
            maxAssetSize: 1000000,
        },
        plugins: pluginsConfig(config, env),
        optimization: optimizationConfig(config, env),
    };
    
    // Add watch configuration in watch mode
    const devServerConfig = env.isWatching ? watchConfig(config, env) : {};
    
    // Merge all configurations
    return merge(
        baseConfig,
        devServerConfig,
        options.overrides || {}
    );
}

export default createWebpackConfig; 