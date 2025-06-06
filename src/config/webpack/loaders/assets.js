/**
 * Asset (images, fonts, etc.) processing configuration
 */
module.exports = () => ({
    test: /\.(ttf|otf|eot|woff2?|png|jpe?g|webp|svg|gif|ico)$/,
    type: 'asset',
    parser: {
        dataUrlCondition: {
            maxSize: 4 * 1024, // 4kb
        },
    },
    generator: {
        // Define the output filename for assets that are emitted as files.
        filename: 'static/[name].[contenthash][ext]',
    },
}); 