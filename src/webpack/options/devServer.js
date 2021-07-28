const url    = require('url');
const config = require('../config.vars.js');

/**
 * We do this to enable injection over SSL.
 */
if (url.parse(config.devHost).protocol === 'https:') {
    process.env.NODE_TLS_REJECT_UNAUTHORIZED = 0;
}

const options = {
    devServer: {
        clientLogLevel: 'silent',
        compress: true,
        inline: true,
        hot: true,
        https: config.useSSLinDev,
        injectClient: true,
        disableHostCheck: true,
        open: config.openBrowserOnWatch,
        overlay: config.showErrorsInBrowser,
        port: config.proxyPort,
        stats: 'normal',
        watchContentBase: true,
        watchOptions: {
            poll: 1200,
            ignored: ["node_modules"]
        },
        writeToDisk: true,
    },
};

/**
 * Use proxy server
 */
if ( config.useProxy )
{
    options.devServer.proxy = {
        '/': {
            target: config.devUrl,
            secure: config.useSSLinDev,
            changeOrigin: true,
            autoRewrite: true,
        },
    };
}

module.exports = options;
