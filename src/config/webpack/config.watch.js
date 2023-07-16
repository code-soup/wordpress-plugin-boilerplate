const url = require('url');
const config = require('./../config');

/**
 * We do this to enable injection over SSL.
 */
if (url.parse(process.env.DEV_URL).protocol === 'https:') {
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
            overlay: true,
        },
        static: {
            directory: config.paths.dist,
            publicPath: config.paths.publicPath,
            serveIndex: false,
        },
        proxy: {
            '/': {
                target: process.env.DEV_URL,
                changeOrigin: true,
                autoRewrite: true,
            },
        },
    },
};
