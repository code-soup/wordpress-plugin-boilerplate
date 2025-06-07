/**
 * Pre-loaders configuration (executed before other loaders)
 */

export default (config) => ({
    enforce: 'pre',
    test: /\.(js|s?[ca]ss)$/,
    include: config.paths.src,
    loader: 'import-glob',
}); 