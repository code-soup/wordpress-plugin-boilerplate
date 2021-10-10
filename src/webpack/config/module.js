const resolver = require('../utils/resolve');
const config   = require( resolver('webpack/config.vars.js') );

// console.log( resolver );

/**
 * Style Loader is used for watch mode, he injects CSS changes without reload
 */
let styleLoader = 'style-loader';

/**
 * When not in watch mode, use MiniCssExtractPlugin.loader
 * Additionally make URL's relative to CSS file instead to WordPress root
 */
if ( ! config.enabled.watcher )
{
    const path = require('path');
    const MiniCssExtractPlugin = require('mini-css-extract-plugin');

    styleLoader = {
        loader: MiniCssExtractPlugin.loader,
        options: {
            publicPath: (resourcePath, context) => {
                // Convert CSS background images to relative URI
                return path.relative(path.dirname(resourcePath), context) + '/';
            },
        },
    }
}

module.exports = {
    rules: [
        {
            enforce: 'pre',
            test: /\.(js|s?[ca]ss)$/,
            include: config.paths.src,
            loader: 'import-glob',
        },
        {
            test: /\.vue$/,
            loader: 'vue-loader',
            options: {
                loaders: {},
            },
        },
        {
            test: /\.js$/,
            exclude: [/node_modules/],
            use: [
                { loader: 'babel-loader' },
            ],
        },
        {
            test: /\.s[ac]ss$/i,
            include: config.paths.src,
            use: [
                styleLoader,
                {
                    loader: 'css-loader',
                    options: { importLoaders: 5 },
                },
                {
                    loader: 'postcss-loader',
                    options: {
                        postcssOptions: {
                            config: resolver('/webpack/config/postcss/config.js'),
                        },
                    },
                },
                { loader: 'sass-loader' },
            ],
        },
        {
            test: /\.(ttf|otf|eot|woff2?|png|jpe?g|gif|svg|ico)$/,
            type: 'asset',
            parser: {
                dataUrlCondition: {
                    maxSize: config.imageSizeInline * 1024,
                },
            },
            include: [
                config.paths.src,
                config.paths.node_modules,
            ],
        },
    ],
};