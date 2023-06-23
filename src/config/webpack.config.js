const { merge } = require("webpack-merge")
const resolver = require("./util/resolve")
const config = require("./config")
const entries = require("./util/dynamic-entry")

let webpackConfig = {
    entry: entries,
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
        logging: "warn",
        modules: false,
        entrypoints: false,
    },
    cache: true,
    target: "web",
    devtool: "inline-source-map",
    module: require("./webpack/config.module"),
    resolve: {
        modules: [config.paths.src, "node_modules"],
        enforceExtension: false,
        alias: {
            utils: resolver("../scripts/util"),
        },
    },
    externals: {
        jquery: "jQuery",
    },
    performance: {
        hints: "error",
        maxEntrypointSize: 1000000,
        maxAssetSize: 1000000,
    },
    plugins: require("./webpack/config.plugins"),
    optimization: require("./webpack/config.optimization"),
}

if (config.enabled.watcher) {
    webpackConfig = merge(webpackConfig, require("./webpack/config.watch"))
}

/**
 * Production only config
 */
if (config.enabled.production) {
    const WebpackAssetsManifest = require("webpack-assets-manifest")

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
        })
    )
}

module.exports = webpackConfig