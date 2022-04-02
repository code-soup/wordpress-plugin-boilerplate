const config  = require('../config.vars.js');
const webpack = require('webpack');

const MiniCssExtractPlugin   = require('mini-css-extract-plugin');
const SVGSpritemapPlugin     = require('svg-spritemap-webpack-plugin');
const { VueLoaderPlugin }    = require('vue-loader');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');

module.exports = [
    new webpack.ProgressPlugin(),
    new CleanWebpackPlugin(),
    new MiniCssExtractPlugin({
        filename: `styles/${config.fileName}.css`,
        chunkFilename: `styles/[id].${config.fileName}.css`,
    }),
    new SVGSpritemapPlugin('src/media/icons/*.svg', {
        output: {
            filename: 'sprite/spritemap-[hash].svg',
            svgo: true,
        },
        styles: 'src/styles/node-modules/_sprites.scss',
    }),
    new webpack.ProvidePlugin({
        $: 'jquery',
        jQuery: 'jquery',
        'window.jQuery': 'jquery',
    }),
    new VueLoaderPlugin(),
];