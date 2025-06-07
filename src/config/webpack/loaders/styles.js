/**
 * Styles (CSS/SASS) processing configuration
 */

import MiniCssExtractPlugin from 'mini-css-extract-plugin';

export default (config, env) => ({
    test: /\.s?[ca]ss$/,
    include: config.paths.src,
    use: [
        env.isWatching
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
                sourceMap: !env.isProduction,
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
                    outputStyle: env.isProduction ? 'compressed' : 'expanded',
                },
            },
        },
    ],
}); 