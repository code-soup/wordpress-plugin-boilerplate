/**
 * JavaScript processing configuration
 */

export default () => ({
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