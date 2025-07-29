/**
 * JavaScript processing configuration
 */

export default () => ({
    test: /\.[jt]sx?$/,
    exclude: [/node_modules/],
    use: [
        {
            loader: 'babel-loader',
            options: {
                cacheDirectory: true,
                presets: [
                    '@babel/preset-env',
                ],
            },
        },
    ],
}); 