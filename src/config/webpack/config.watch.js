const url = require('url');
const config = require('./../config');

/**
 * We do this to enable injection over SSL.
 */
if (url.parse(process.env.WP_DEV_URL).protocol === 'https:') {
    process.env.NODE_TLS_REJECT_UNAUTHORIZED = 0;
}

module.exports = {
    devServer: {
        hot: true,
        port: process.env.DEV_PROXY_PORT,
        compress: true,
        allowedHosts: 'all',
        watchFiles: {
            paths: ['templates/**/*.php', 'includes/**/*', 'src/**/*'],
            options: {
                usePolling: true,
            },
        },
        client: {
            logging: 'info',
            overlay: false,
        },
        static: {
            directory: config.paths.dist,
            publicPath: config.paths.publicPath,
            serveIndex: false,
        },
        proxy: [
            {
                context: ['/'],
                target: process.env.WP_DEV_URL,
                changeOrigin: true,
                autoRewrite: true,
            },
        ],
        devMiddleware: {
            publicPath: config.paths.publicPath,
            serverSideRender: false,
            writeToDisk: true,
        },
    },
};
