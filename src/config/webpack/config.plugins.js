/**
 * Webpack plugins configuration
 * Uses utilities for conditional plugin loading
 */

import { WebpackManifestPlugin } from 'webpack-manifest-plugin';
import MiniCssExtractPlugin from 'mini-css-extract-plugin';
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

        // Manifest plugin - always generate manifest for asset mapping
        new WebpackManifestPlugin({
            fileName: 'manifest.json',
            publicPath: '',
            writeToFileEmit: true,
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
                                svg4everybody: false,
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
