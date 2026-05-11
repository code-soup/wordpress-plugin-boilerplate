/**
 * Styles (CSS/SASS) processing configuration
 */

import MiniCssExtractPlugin from 'mini-css-extract-plugin';

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
                    ],
                },
                sourceMap: !isProduction,
            },
        },
        {
            loader: 'resolve-url-loader',
            options: {
                sourceMap: !isProduction,
            },
        },
        {
            loader: 'sass-loader',
            options: {
                sourceMap: !isProduction,
                sassOptions: {
                    outputStyle: isProduction ? 'compressed' : 'expanded',
                },
            },
        },
    ],
}); 