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
        http2: true,
        compress: true,
        allowedHosts: 'all',
        devMiddleware: {
            publicPath: config.paths.publicPath,
            writeToDisk: true,
        },
        static: {
            directory: config.paths.root,
            watch: {
                ignored: ['node_modules'],
            },
        },
        client: {
            logging: 'info',
            overlay: config.showErrorsInBrowser,
        },
        hot: true,
        https: false,
        open: config.openBrowserOnWatch,
        port: config.proxyPort,
        proxy: false,
        webSocketServer: false,
    },
};

/**
 * Use proxy server
 */
if ( config.useProxy )
{
    options.devServer.proxy = {
        '/': {
            target: 'http://neki.site',
            ssl: false,
            changeOrigin: true,
            autoRewrite: true,
        },
    };
}

module.exports = options;
