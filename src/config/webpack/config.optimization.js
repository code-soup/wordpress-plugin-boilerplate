/**
 * Webpack optimization configuration
 */

import TerserPlugin from 'terser-webpack-plugin';
import CssMinimizerPlugin from 'css-minimizer-webpack-plugin';

export default (config, { isProduction }) => ({
    // Enable tree shaking and module concatenation in production
    usedExports: true,
    concatenateModules: isProduction,
    sideEffects: true,

    // Set chunk and module IDs to be deterministic in production for long-term caching
    chunkIds: isProduction ? 'deterministic' : 'named',
    moduleIds: isProduction ? 'deterministic' : 'named',

    // Extract webpack runtime into a single chunk for better caching
    runtimeChunk: 'single',
    
    // Code splitting configuration
    splitChunks: {
        chunks: 'all',
        cacheGroups: {
            default: false,
            vendors: false, // Turn off default behavior
            // Group all vendor code from node_modules into a single chunk
			vendor: {
				name: 'vendor-libs',
				test: /[\\/]node_modules[\\/]/,
				chunks: 'all',
				enforce: true,
			},
        },
    },
    
    // Minification configuration (production only)
    minimize: isProduction,
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
