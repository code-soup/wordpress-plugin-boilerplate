const TerserPlugin = require('terser-webpack-plugin');

function normalizeName(name) {
    return name
        .replace(/node_modules/g, 'nodemodules')
        .replace(/[\-_.|]+/g, ' ')
        .replace(/\b(nodemodules|js|modules|es)\b/g, '')
        .trim()
        .replace(/ +/g, '-');
}

module.exports = {
    splitChunks: {
        chunks: 'async',
        name(module, chunks, cacheGroupKey) {
            const moduleFileName = module
                .identifier()
                .split('/')
                .reduceRight((item) => item);
            return (
                'vendor/' + normalizeName(moduleFileName.replace(/[\/]/g, '-'))
            );
        },
    },
    minimize: true,
    minimizer: [
        new TerserPlugin({
            parallel: true,
            terserOptions: {
                compress: true,
                safari10: true,
            },
        }),
    ],
};
