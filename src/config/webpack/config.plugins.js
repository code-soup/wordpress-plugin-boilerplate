const config = require("../config");
const webpack = require("webpack");

const { CleanWebpackPlugin } = require("clean-webpack-plugin");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const StyleLintPlugin = require("stylelint-webpack-plugin");
const CopyGlobsPlugin = require("copy-globs-webpack-plugin");
const FriendlyErrorsWebpackPlugin = require("friendly-errors-webpack-plugin");
const SVGSpritemapPlugin = require("svg-spritemap-webpack-plugin");

module.exports = [
    new CleanWebpackPlugin(),
    new SVGSpritemapPlugin("src/icons/*.svg", {
        output: {
            svg4everybody: false,
            filename: `sprite/spritemap.svg`,
            svgo: true,
        },
        styles: "src/styles/_npm/_sprites.scss",
    }),
    new CopyGlobsPlugin({
        pattern: config.copy,
        output: "[path][name].[ext]",
    }),
    new MiniCssExtractPlugin({
        filename: `styles/${config.fileName}.css`,
        chunkFilename: `styles/[id].${config.fileName}.css`,
    }),
    new webpack.ProvidePlugin({
        $: "jquery",
        jQuery: "jquery",
        "window.jQuery": "jquery",
    }),
    new StyleLintPlugin({
        failOnError: config.enabled.production,
        syntax: "scss",
    }),
    new FriendlyErrorsWebpackPlugin(),
];