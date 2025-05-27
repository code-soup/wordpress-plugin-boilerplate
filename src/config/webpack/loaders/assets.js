/**
 * Asset (images, fonts, etc.) processing configuration
 */

const svgToMiniDataURI = require('mini-svg-data-uri');

module.exports = (config, env) => ({
    test: /\.(ttf|otf|eot|woff2?|png|jpe?g|svg|gif|ico)$/,
    type: 'asset',
    parser: {
        dataUrlCondition: {
            maxSize: 4 * 1024, // 4kb
        },
    },
    generator: {
        filename: 'static/[name].[contenthash][ext]',
        dataUrl: (content) => {
            content = content.toString();
            return content.includes('<svg') 
                ? svgToMiniDataURI(content)
                : content;
        },
    },
}); 