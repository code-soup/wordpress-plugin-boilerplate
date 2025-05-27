/**
 * Webpack development server configuration
 */

const url = require('url');
const config = require('./../config');

// Determine environment safety settings
const isDev = process.env.NODE_ENV !== 'production';
const httpsUrl = process.env.WP_DEV_URL && url.parse(process.env.WP_DEV_URL).protocol === 'https:';

/**
 * Safer approach for development SSL
 */
if (httpsUrl && isDev) {
    console.warn('\x1b[33m%s\x1b[0m', 'Warning: Using HTTPS in development. For a more secure approach, consider proper certificates.');
    // Only disable certificate validation in development
    process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0';
}

module.exports = (config, env) => {
    // Handle HTTPS certificate validation in development mode
    if (httpsUrl && env.isDev) {
        console.warn('\x1b[33m%s\x1b[0m', 'Warning: Using HTTPS in development. For a more secure approach, consider proper certificates.');
        // Only disable certificate validation in development
        process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0';
    }

    return {
        devServer: {
            hot: true,
            port: process.env.DEV_PROXY_PORT || 8080,
            compress: true,
            allowedHosts: 'all',
            watchFiles: {
                paths: [
                    `${config.paths.root}/templates/**/*.php`, 
                    `${config.paths.root}/includes/**/*`, 
                    `${config.paths.src}/**/*`
                ],
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
            proxy: process.env.WP_DEV_URL ? [
                {
                    context: ['/'],
                    target: process.env.WP_DEV_URL,
                    changeOrigin: true,
                    autoRewrite: true,
                    secure: !env.isDev, // Only validate certificates in production
                },
            ] : undefined,
            devMiddleware: {
                publicPath: config.paths.publicPath,
                serverSideRender: false,
                writeToDisk: true,
            },
        },
    };
};
