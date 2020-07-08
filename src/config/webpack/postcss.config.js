const cssnanoConfig = {
    preset: ['default', { discardComments: { removeAll: true } }],
};

module.exports = ({ options }) => {
    return {
        plugins: {
            autoprefixer: true,
            cssnano: options.enabled.production ? cssnanoConfig : false,
        },
    };
};