const config = require('../../config.vars.js');

const cssnanoConfig = {
    preset: [
        'default',
        { discardComments: { removeAll: true } },
    ],
};

module.exports = {
    plugins: {
        'postcss-preset-env': true,
        'cssnano': config.enabled.production
            ? cssnanoConfig
            : false,
    },
};