/**
 * Webpack optimization configuration
 */

const TerserPlugin = require('terser-webpack-plugin');

/**
 * Normalize module name for better chunk naming
 * @param {string} name - The module name to normalize
 * @return {string} Normalized name
 */
function normalizeName(name) {
    return name
        .replace(/node_modules/g, 'nodemodules')
        .replace(/[-_.|]+/g, ' ')
        .replace(/\b(nodemodules|js|modules|es)\b/g, '')
        .trim()
        .replace(/ +/g, '-');
}

module.exports = (config, env) => ({
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
            defaultVendors: {
                test: /[\\/]node_modules[\\/]/,
                priority: -10,
                reuseExistingChunk: true,
                name(module) {
                    const moduleFileName = module
                        .identifier()
                        .split('/')
                        .reduceRight((item) => item);
                    return 'vendor/' + normalizeName(moduleFileName.replace(/[/]/g, '-'));
                },
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
        ...(env.isProduction ? [
            new TerserPlugin({
                parallel: true,
                terserOptions: {
                    compress: true,
                    safari10: true,
                },
            }),
        ] : []),
    ],
});
