/**
 * Webpack plugins configuration
 * Uses utilities for conditional plugin loading
 */

const fs = require('fs');
const webpack = require('webpack');
const { WebpackManifestPlugin } = require('webpack-manifest-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const StyleLintPlugin = require('stylelint-webpack-plugin');
const ESLintPlugin = require('eslint-webpack-plugin');
const { BundleAnalyzerPlugin } = require('webpack-bundle-analyzer');

// Import utilities
const { conditionalPlugins, lazyLoadPlugin } = require('../util/plugins');

module.exports = (config, env) => {
    // Check if SVG icons directory has files
    const hasSvgIcons = fs.existsSync(config.paths.icons) && 
                      fs.readdirSync(config.paths.icons).some(file => 
                        file.endsWith('.svg') && 
                        !file.startsWith('.')
                      );
    
    // Base plugins that are always included
    const basePlugins = [
        new MiniCssExtractPlugin({
            filename: `styles/${config.fileName}.css`,
            chunkFilename: `styles/[id].${config.fileName}.css`,
        }),
        new webpack.ProvidePlugin({
            $: 'jquery',
            jQuery: 'jquery',
            'window.jQuery': 'jquery',
        }),
    ];
    
    // Conditional plugins based on build context
    const contextPlugins = conditionalPlugins([
        // SVG Spritemap plugin - only when SVG files exist
        {
            condition: hasSvgIcons,
            factory: () => lazyLoadPlugin('svg-spritemap-webpack-plugin', Plugin => 
                new Plugin(`${config.paths.icons}/**/*.svg`, {
                    output: {
                        filename: `images/spritemap.[contenthash].svg`,
                        svg4everybody: true,
                    },
                    sprite: {
                        prefix: 'icon-',
                        generate: {
                            title: false,
                        },
                    },
                }),
            ),
        },
        
        // Manifest plugin - only in production
        {
            condition: env.isProduction,
            factory: () => new WebpackManifestPlugin(),
        },
        
        // ESLint plugin - only when running lint commands
        {
            condition: env.isLintingScripts,
            factory: () => new ESLintPlugin({
                extensions: ['js'],
                emitWarning: !env.isProduction,
                failOnError: env.isProduction,
                context: config.paths.src,
            }),
        },
        
        // StyleLint plugin - only when style lint commands
        {
            condition: env.isLintingStyles,
            factory: () => new StyleLintPlugin({
                failOnError: env.isProduction,
                syntax: "scss",
            }),
        },
        
        // Bundle analyzer - only when analyzing
        {
            condition: env.isAnalyzing,
            factory: () => new BundleAnalyzerPlugin({
                analyzerMode: 'server',
                analyzerPort: 8888,
                openAnalyzer: true,
                generateStatsFile: !!process.env.CI,
                statsFilename: 'stats.json',
            }),
        },
    ]);
    
    // Combine all plugins
    return [...basePlugins, ...contextPlugins];
};
