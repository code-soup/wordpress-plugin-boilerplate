/**
 * Webpack configuration factory
 * Centralizes creation of webpack configurations
 */

const { merge } = require('webpack-merge');

// Import utilities
const env = require('./util/env');
const pathUtils = require('./util/paths');

/**
 * Creates a webpack configuration based on environment and options
 * 
 * @param {Object} options - Configuration options
 * @return {Object} Webpack configuration object
 */
function createWebpackConfig(options = {}) {
    // Use provided config or the default one
    const config = options.config || require('./config');
    
    // Build the base webpack configuration
    const baseConfig = {
        entry: require('./util/dynamic-entry')(),
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
        module: require('./webpack/config.module')(config, env),
        resolve: {
            modules: [config.paths.src, 'node_modules'],
            extensions: ['*', '.js'],
            enforceExtension: false,
            alias: {
                '@utils': pathUtils.fromSrc('scripts/util'),
                '@styles': pathUtils.fromSrc('styles'),
                '@scripts': pathUtils.fromSrc('scripts'),
                '@icons': pathUtils.fromSrc('icons'),
                '@images': pathUtils.fromSrc('images'),
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
        plugins: require('./webpack/config.plugins')(config, env),
        optimization: require('./webpack/config.optimization')(config, env),
    };
    
    // Add watch configuration in watch mode
    const watchConfig = env.isWatching ? require('./webpack/config.watch')(config, env) : {};
    
    // Merge all configurations
    return merge(
        baseConfig,
        watchConfig,
        options.overrides || {}
    );
}

module.exports = createWebpackConfig; 