/**
 * Styles (CSS/SASS) processing configuration
 */

import MiniCssExtractPlugin from 'mini-css-extract-plugin';
// Import the module itself, which is an object
import purgecssModule from '@fullhuman/postcss-purgecss';

// Create a new, corrected wrapper function
const purgeCssPlugin = (opts) => {
    // The actual function is on the .default property of the imported module
    const purgecss = purgecssModule.default || purgecssModule;
    return purgecss(opts);
};

export default (config, { isProduction, isWatching }) => ({
    test: /\.s?[ca]ss$/,
    include: config.paths.src,
    use: [
        isWatching
            ? 'style-loader'
            : {
                  loader: MiniCssExtractPlugin.loader,
                  options: {
                      esModule: true,
                  },
              },
        {
            loader: 'css-loader',
            options: {
                sourceMap: !isProduction,
                importLoaders: 3,
                esModule: true,
            },
        },
        {
            loader: 'postcss-loader',
            options: {
                postcssOptions: {
                    plugins: [
                        ['postcss-preset-env', {
                            stage: 3,
                            features: {
                                'nesting-rules': true,
                            },
                        }],
                        ...(isProduction
                            ? [purgeCssPlugin({
                                content: [
                                    './**/*.php',
                                    './src/**/*.js',
                                    './src/**/*.jsx',
                                    './src/**/*.ts',
                                    './src/**/*.tsx',
                                ],
                                safelist: {
                                    standard: [/^wp-/, /^admin-/, /^icon-/, /^is-/, /^has-/], // adjust as needed
                                },
                                defaultExtractor: content => content.match(/[\w-/:]+(?<!:)/g) || [],
                            })]
                            : []),
                    ],
                },
                sourceMap: true,
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
                sassOptions: {
                    outputStyle: isProduction ? 'compressed' : 'expanded',
                },
            },
        },
    ],
}); 