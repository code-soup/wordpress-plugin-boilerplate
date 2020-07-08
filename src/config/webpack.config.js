const merge = require("webpack-merge");
const resolver = require("./util/resolve");
const config = require("./config");

let webpackConfig = {
    entry: config.entry,
    context: config.paths.src,
    output: {
        path: config.paths.path,
        publicPath: config.paths.publicPath,
        filename: `scripts/${config.fileName}.js`,
    },
    mode: config.mode,
    stats: {
        assets: true,
        colors: true,
        logging: 'warn',
        modules: false,
        entrypoints: false,
    },
    devtool: config.enabled.production ? false : "cheap-module-source-map",
    module: require("./webpack/config.module"),
    resolveLoader: {
        moduleExtensions: ["-loader"],
    },
    resolve: {
        modules: [config.paths.src, "node_modules"],
        enforceExtension: false,
        alias: {
            utils: resolver("../scripts/util"),
            widgets: resolver("../scripts/widgets"),
            components: resolver("../scripts/components"),
        },
    },
    externals: {
        jquery: "jQuery",
    },
    performance: {
        hints: "error",
    },
    plugins: require("./webpack/config.plugins"),
    optimization: require("./webpack/config.optimization"),
};

/**
 * Include webpack-dev-server config
 */
if (config.enabled.watcher) {
    webpackConfig = merge(webpackConfig, require("./webpack/config.watch"));
}

/**
 * Production only config
 */
if (config.enabled.production)
{
    const { default: ImageminPlugin } = require("imagemin-webpack-plugin");
    const imageminMozjpeg = require("imagemin-mozjpeg");
    const WebpackAssetsManifest = require("webpack-assets-manifest");

    /**
     * Additional plugins for production build
     */
    webpackConfig.plugins.push(
        /**
         * Assets versioning with manifest.json
         */
        new WebpackAssetsManifest({
            output: "assets.json",
            space: 4,
            writeToDisk: true,
            assets: {},
            replacer: require("./util/assetManifestsReplacer"),
        }),
        /**
         * Optimize images from assets folder
         */
        new ImageminPlugin({
            optipng: { optimizationLevel: 2 },
            gifsicle: { optimizationLevel: 3 },
            pngquant: { quality: "65-90", speed: 4 },
            svgo: {
                plugins: [
                    { removeUnknownsAndDefaults: false },
                    { cleanupIDs: false },
                    { removeViewBox: false },
                ],
            },
            plugins: [imageminMozjpeg({ quality: 80 })],
        })
    );
}

module.exports = webpackConfig;