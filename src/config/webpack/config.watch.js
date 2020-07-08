const url = require('url');
const config = require('../config');

/**
 * We do this to enable injection over SSL.
 */
if (url.parse(config.devUrl).protocol === 'https:') {
    process.env.NODE_TLS_REJECT_UNAUTHORIZED = 0;
}

module.exports = {
    devServer: {
        compress: true,
        watchOptions: {
            poll: true,
            aggregateTimeout: 300,
        },
        https: config.useSSLinDev,
        stats: 'errors-only',
        hot: true,
        port: config.proxyPort,
        overlay: config.showErrorsInBrowser,
        open: config.openBrowserOnWatch,
        writeToDisk: true,
        clientLogLevel: 'silent',
        proxy: {
            '/': {
                target: config.devUrl,
                secure: config.useSSLinDev,
                changeOrigin: true,
                autoRewrite: true,
            },
        },
    },
};
