/**
 * Webpack development server configuration
 */
import { URL } from 'url';

// Determine if the target WordPress dev URL is HTTPS using the modern URL API
let targetIsHttps = false;
if (process.env.WP_DEV_URL) {
    try {
        const devUrl = new URL(process.env.WP_DEV_URL);
        targetIsHttps = devUrl.protocol === 'https:';
    } catch {
        console.error(`\n[Webpack Config] Error: Invalid URL provided for WP_DEV_URL: "${process.env.WP_DEV_URL}"`);
        console.error('[Webpack Config] Please ensure it is a full URL (e.g., http://localhost:8000).\n');
        // Assuming http and letting the proxy fail later if it's a real issue.
        targetIsHttps = false;
    }
}

// Define the dev server's host and port.
// The dev server itself runs on HTTP unless configured otherwise.
const devServerHost = 'localhost';
const devServerPort = process.env.DEV_PROXY_PORT || 8080;

export default (config, env) => {
    // Handle self-signed certificate for the PROXY TARGET if it's HTTPS
    if (targetIsHttps && env.isDevelopment) {
        console.warn('\x1b[33m%s\x1b[0m', '[Webpack DevServer] Proxying to an HTTPS target. Allowing self-signed certificates.');
        process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0';
    }

    return {
        devServer: {
            host: devServerHost,
            port: devServerPort,
            hot: true,
            compress: true,
            allowedHosts: 'all',
            watchFiles: {
                paths: [
                    `${config.paths.root}/templates/**/*.php`,
                    `${config.paths.root}/includes/**/*.php`,
                ],
                options: {
                    usePolling: false,
                },
            },
            client: {
                logging: 'info',
                overlay: {
                    errors: true,
                    warnings: false,
                },
                // This is the key change:
                // It tells the client-side script to connect to this specific URL for updates.
                // The protocol is 'ws' because this dev server is running on HTTP.
                webSocketURL: `ws://${devServerHost}:${devServerPort}/ws`,
            },
            static: {
                directory: config.paths.dist,
                publicPath: config.publicPath,
                serveIndex: false,
                watch: false,
            },
            proxy: process.env.WP_DEV_URL ? [{
                context: ['/'],
                target: process.env.WP_DEV_URL,
                changeOrigin: true,
                autoRewrite: true,
                secure: !env.isProduction,
            }] : undefined,
            devMiddleware: {
                publicPath: config.publicPath,
                serverSideRender: false,
                writeToDisk: true,
            },
        },
    };
};
