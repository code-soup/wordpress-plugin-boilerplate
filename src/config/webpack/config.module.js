/**
 * Webpack modules
 */

const path = require("path");
const config = require("../config");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

module.exports = {
    rules: [
        {
            enforce: "pre",
            test: /\.(js|s?[ca]ss)$/,
            include: config.paths.src,
            loader: "import-glob",
        },
        {
            test: /\.js$/,
            exclude: [/node_modules/],
            use: [
                { loader: "babel-loader" }
            ],
        },
        {
            test: /\.s?[ca]ss$/,
            include: config.paths.src,
            use: [
                config.enabled.watcher
                    ? "style-loader"
                    : MiniCssExtractPlugin.loader,
                {
                    loader: "css-loader",
                    options: { sourceMap: !config.enabled.production },
                },
                {
                    loader: "postcss-loader",
                    options: {
                        postcssOptions: {
                            plugins: [
                                ["postcss-preset-env"],
                            ],
                        },
                    },
                },
                {
                    loader: "resolve-url-loader",
                    options: {
                        sourceMap: !config.enabled.production,
                    },
                },
                {
                    loader: "sass-loader",
                    options: {
                        sourceMap: !config.enabled.production,
                    },
                },
            ],
        },
        {
            test: /\.(ttf|otf|eot|woff2?|png|jpe?g|gif|svg|ico)$/,
            include: config.paths.src,
            loader: "url-loader",
            options: {
                limit: 4096,
                name: `[path]${config.fileName}.[ext]`,
            },
        },
        {
            test: /\.(ttf|otf|eot|woff2?|png|jpe?g|gif|svg|ico)$/,
            include: /node_modules/,
            loader: "url-loader",
            options: {
                limit: 4096,
                outputPath: "vendor/",
                name: `${config.fileName}.[ext]`,
            },
        },
    ],
};