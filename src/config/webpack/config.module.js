/**
 * Webpack modules
 */
const config = require('../config');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const svgToMiniDataURI = require('mini-svg-data-uri');

module.exports = {
    rules: [
        {
            enforce: 'pre',
            test: /\.(js|s?[ca]ss)$/,
            include: config.paths.src,
            loader: 'import-glob',
        },
        {
            test: /\.js$/,
            exclude: [/node_modules/],
            use: [{ loader: 'babel-loader' }],
        },
        {
            test: /\.s?[ca]ss$/,
            include: config.paths.src,
            use: [
                process.env.WEBPACK_SERVE
                    ? 'style-loader'
                    : MiniCssExtractPlugin.loader,
                {
                    loader: 'css-loader',
                    options: { sourceMap: !config.enabled.production },
                },
                {
                    loader: 'postcss-loader',
                    options: {
                        postcssOptions: {
                            plugins: [['postcss-preset-env']],
                        },
                    },
                },
                {
                    loader: 'resolve-url-loader',
                    options: {
                        sourceMap: true,
                    },
                },
                {
                    loader: 'sass-loader',
                    options: {
                        sourceMap: true,
                    },
                },
            ],
        },
        {
            test: /\.(ttf|otf|eot|woff2?|png|jpe?g|svg|gif|ico)$/,
            type: 'asset',
            generator: {
                filename: 'static/[name]-[hash][ext]',
                dataUrl: (content) => {
                    content = content.toString();
                    return svgToMiniDataURI(content);
                },
            },
        },
        // {
        //     test: /\.(ttf|otf|eot|woff2?|png|jpe?g|gif|svg|ico)$/,
        //     include: /node_modules/,
        //     loader: 'url-loader',
        //     options: {
        //         limit: 4096,
        //         outputPath: 'vendor/',
        //         name: `${config.fileName}.[ext]`,
        //     },
        // },
    ],
};
