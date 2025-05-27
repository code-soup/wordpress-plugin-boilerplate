/**
 * JavaScript processing configuration
 */

module.exports = (config, env) => ({
    test: /\.js$/,
    exclude: [/node_modules/],
    use: [
        {
            loader: 'babel-loader',
            options: {
                cacheDirectory: true,
            },
        },
    ],
}); 