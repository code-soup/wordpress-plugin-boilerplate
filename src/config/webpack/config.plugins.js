const config = require('../config');
const webpack = require('webpack');

const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const StyleLintPlugin = require('stylelint-webpack-plugin');

module.exports = [
    new CleanWebpackPlugin(),
    new MiniCssExtractPlugin({
        filename: `styles/${config.fileName}.css`,
        chunkFilename: `styles/[id].${config.fileName}.css`,
    }),
    new webpack.ProvidePlugin({
        $: 'jquery',
        jQuery: 'jquery',
        'window.jQuery': 'jquery',
    }),
    // new StyleLintPlugin({
    //     failOnError: config.enabled.production,
    //     syntax: "scss",
    // }),
];
