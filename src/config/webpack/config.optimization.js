/**
 * Webpack optimization configuration
 */

import TerserPlugin from 'terser-webpack-plugin';
import CssMinimizerPlugin from 'css-minimizer-webpack-plugin';

export default (config, env) => ({
    // Enable tree shaking and module concatenation in production
    usedExports: true,
    concatenateModules: env.isProduction,
    sideEffects: true,

    // Set chunk and module IDs to be deterministic in production for long-term caching
    chunkIds: env.isProduction ? 'deterministic' : 'named',
    moduleIds: env.isProduction ? 'deterministic' : 'named',

    // Extract webpack runtime into a single chunk for better caching
    runtimeChunk: 'single',
    
    // Code splitting configuration
    splitChunks: {
        chunks: 'all',
        maxInitialRequests: 5,
        maxAsyncRequests: 20,
        minSize: 20000,
        cacheGroups: {
            default: false,
            vendors: false,
            defaultVendors: {
                test: /[\\/]node_modules[\\/]/,
                priority: -10,
                reuseExistingChunk: true,
                name: 'vendor-libs',
            },
            commons: {
                name: 'commons',
                minChunks: 2,
                priority: -20,
                reuseExistingChunk: true,
                enforce: true,
            },
        },
    },
    
    // Minification configuration (production only)
    minimize: env.isProduction,
    minimizer: [
        new TerserPlugin({
            parallel: true,
            terserOptions: {
                compress: true,
                safari10: true,
            },
        }),
        new CssMinimizerPlugin(),
    ],
});
