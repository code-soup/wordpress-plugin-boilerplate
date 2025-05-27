/**
 * Styles (CSS/SASS) processing configuration
 */

const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = (config, env) => ({
    test: /\.s?[ca]ss$/,
    include: config.paths.src,
    use: [
        process.env.WEBPACK_SERVE
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
                        // Add cssnano for CSS minification in production mode
                        env.isProduction ? ['cssnano', {
                            preset: ['default', {
                                discardComments: {
                                    removeAll: true,
                                },
                                normalizeWhitespace: true,
                            }],
                        }] : false,
                    ].filter(Boolean),
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