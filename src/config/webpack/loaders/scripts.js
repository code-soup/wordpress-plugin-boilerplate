/**
 * JavaScript processing configuration
 */

module.exports = () => ({
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