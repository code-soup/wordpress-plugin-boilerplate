const TerserPlugin = require('terser-webpack-plugin');

module.exports = {
    mangleWasmImports: true,
    removeAvailableModules: true,
    removeEmptyChunks: true,
    mergeDuplicateChunks: true,
    flagIncludedChunks: true,
    splitChunks: {
        chunks: 'all',
        minSize: 10000,
        maxSize: 70000,
        minChunks: 1,
        maxAsyncRequests: 6,
        maxInitialRequests: 4,
        automaticNameDelimiter: '~',
        cacheGroups: {
            defaultVendors: {
                test: /[\\/]node_modules[\\/]/,
                priority: -10,
            },
            default: {
                minChunks: 2,
                priority: -20,
                reuseExistingChunk: true,
            },
        },
    },
    minimize: true,
    minimizer: [
        new TerserPlugin({
            parallel: true,
            terserOptions: {
                ecma: undefined,
                compress: true,
                safari10: true,
            },
        }),
    ],
};
