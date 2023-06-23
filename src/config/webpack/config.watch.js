const url    = require('url');
const dotenv = require('dotenv');
dotenv.config();

/**
 * We do this to enable injection over SSL.
 */
if (url.parse( process.env.DEV_URL ).protocol === 'https:') {
    process.env.NODE_TLS_REJECT_UNAUTHORIZED = 0;
}

module.exports = {
    devServer: {
        compress: true,
        watchOptions: {
            poll: true,
            aggregateTimeout: 300,
        },
        stats: 'errors-only',
        hot: true,
        port: process.env.DEV_PROXY_PORT,
        writeToDisk: true,
        clientLogLevel: 'silent',
        proxy: {
            '/': {
                target: process.env.DEV_URL,
                secure: false,
                changeOrigin: true,
                autoRewrite: true,
            },
        },
    },
};
