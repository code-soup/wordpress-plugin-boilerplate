const resolver = require('./utils/resolve');
const config   = require( resolver('/webpack/config.vars.js' ) );

const { merge } = require('webpack-merge');

/**
 * Webpack configuration object
 * Webpack v5.35.1
 *
 * @link {https://webpack.js.org/configuration/}
 */
let webpackConfig = {
    entry: config.entry,
    context: config.paths.src,
    output: {
        path: config.paths.dist,
        pathinfo: ! config.enabled.production,
        publicPath: config.enabled.production
            ? config.paths.publicPathProd
            : config.paths.publicPath,
        filename: `scripts/${config.fileName}.js`,
        assetModuleFilename: config.assetFilename,
    },
    mode: config.mode,
    stats: {
        assets: false,
        colors: true,
        logging: 'warn',
        modules: false,
        entrypoints: true,
    },
    devtool: config.enabled.production
        ? false
        : 'cheap-module-source-map',
    module: require('./options/module'),
    resolve: {
        preferRelative: true,
        modules: [
            config.paths.src,
            config.paths.node_modules,
        ],
        alias: {
            utils: resolver('scripts/utils'),
            images: resolver('/media/images'),
            icons: resolver('/media/icons'),
        },
    },
    externals: {
        jquery: 'jQuery',
    },
    performance: {
        hints: 'warning',
    },
    plugins: require('./options/plugins'),
};

/**
 * Include HMR and watch config
 */
if (config.enabled.watcher) {

    // Dev server
    webpackConfig = merge(webpackConfig, require("./options/devServer"));
}


/**
 * Optimize images from assets folder 
 */
if (config.enabled.production && config.optimizeImages )
{
    const ImageMinimizerPlugin = require('image-minimizer-webpack-plugin');

    webpackConfig.plugins.push(
        new ImageMinimizerPlugin({
            minimizerOptions: {
                plugins: [
                    ['gifsicle', { interlaced: true }],
                    ['jpegtran', { progressive: true }],
                    ['optipng', { optimizationLevel: 5 }],
                    ['svgo', {
                        plugins: [{removeViewBox: false}],
                    }],
                ],
            },
        })
    );
}


/**
 * Production only config
 */
if (config.enabled.production)
{
    const WebpackAssetsManifest = require('webpack-assets-manifest');
    const StyleLintPlugin       = require('stylelint-webpack-plugin');
    const ESLintPlugin          = require('eslint-webpack-plugin');

    webpackConfig.plugins.push(
        
        // Assets versioning with manifest.json
        new WebpackAssetsManifest({
            output: 'assets.json',
            space: 4,
            writeToDisk: true,
            assets: {},
            replacer: require('./utils/assetManifestsReplacer'),
        }),

        new StyleLintPlugin({
            failOnError: true,
            syntax: 'scss',
        }),

        new ESLintPlugin(), 
    );

    // Optimize
    webpackConfig.optimization = require('./options/optimization');
}

module.exports = webpackConfig;