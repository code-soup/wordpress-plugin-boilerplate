/**
 * Webpack plugins configuration
 * Uses utilities for conditional plugin loading
 */

import webpack from 'webpack';
import { WebpackManifestPlugin } from 'webpack-manifest-plugin';
import MiniCssExtractPlugin from 'mini-css-extract-plugin';
import StyleLintPlugin from 'stylelint-webpack-plugin';
import ESLintPlugin from 'eslint-webpack-plugin';
import { BundleAnalyzerPlugin } from 'webpack-bundle-analyzer';
import SVGSpritemapPlugin from 'svg-spritemap-webpack-plugin';

// Import utilities
import { conditionalPlugins, configurePlugin } from '../util/plugins.js';

export default (config, env, fileName) => {
    // Base plugins that are always included
    const basePlugins = [
        new MiniCssExtractPlugin({
            filename: `styles/${fileName}.css`,
            chunkFilename: `styles/${env.isProduction ? '[id].[contenthash]' : '[id]'}.chunk.css`,
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
            condition: env.hasSvgIcons,
            factory: () =>
                configurePlugin(
                    SVGSpritemapPlugin,
                    (Plugin) =>
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
                        })
                ),
        },
        
        // Manifest plugin - always generate manifest for asset mapping
        {
            condition: true, // Always generate manifest
            factory: () => new WebpackManifestPlugin({
                fileName: 'manifest.json',
                publicPath: '',
                writeToFileEmit: true,
            }),
        },
        
        // ESLint plugin - only when running lint commands
        {
            condition: env.isLintingScripts,
            factory: () =>
                new ESLintPlugin({
                    extensions: ['js'],
                    emitWarning: !env.isProduction,
                    failOnError: env.isProduction,
                    context: config.paths.src,
                }),
        },
        
        // StyleLint plugin - only when style lint commands
        {
            condition: env.isLintingStyles,
            factory: () =>
                new StyleLintPlugin({
                    failOnError: env.isProduction,
                    syntax: 'scss',
                }),
        },
        
        // Bundle analyzer - only when analyzing
        {
            condition: env.isAnalyzing,
            factory: () =>
                new BundleAnalyzerPlugin({
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
