/**
 * Webpack development server configuration
 *
 * Environment variables
 * ---------------------
 * WP_DEV_URL     → URL of the WordPress instance that webpack-dev-server should proxy requests TO.
 * DEV_PROXY_URL  → URL (protocol://host:port) where webpack-dev-server itself should listen.
 * DEV_PROXY_PORT → Optional convenience override for the port portion of DEV_PROXY_URL.
 */
import { URL } from "url";
import { parseUrl } from "../util/url.js";

/**
 * Parse a URL and return its components in a convenient shape.
 * Exits the process on invalid input so that mis-configured .env files
 * fail fast instead of silently falling back to defaults.
 *
 * @param {string} value        The URL to parse.
 * @param {() => object} [fallback] Function producing a fallback return value.
 * @returns {{ host: string, port: number|undefined, protocol: string, isHttps: boolean, url: URL }}
 */
const parseUrl = (value, fallback) => {
	try {
		const u = new URL(value);
		return {
			host: u.hostname,
			port: u.port ? Number(u.port) : undefined,
			protocol: u.protocol,
			isHttps: u.protocol === "https:",
			url: u,
		};
	} catch {
		console.error(`\n[Webpack Config] Error: Invalid URL provided: "${value}"`);
		if (fallback) {
			return typeof fallback === "function" ? fallback() : fallback;
		}
		process.exit(1);
	}
};

// -----------------------------------------------------------------------------
// Resolve target WordPress URL (proxy target)
// -----------------------------------------------------------------------------
const wpTargetInfo = process.env.WP_DEV_URL
	? parseUrl(process.env.WP_DEV_URL)
	: null;

// -----------------------------------------------------------------------------
// Resolve webpack dev-server host / port
// -----------------------------------------------------------------------------
const DEFAULT_DEV_SERVER = "http://localhost:8080";

let rawDevServerUrl = process.env.DEV_PROXY_URL || DEFAULT_DEV_SERVER;

// (9) Allow DEV_PROXY_PORT to override the port portion *before* parsing
if (process.env.DEV_PROXY_PORT) {
	try {
		const tmp = new URL(rawDevServerUrl);
		tmp.port = String(process.env.DEV_PROXY_PORT);
		rawDevServerUrl = tmp.toString();
	} catch {
		// The parseUrl() call below will handle the invalid URL and exit.
	}
}

const devServerInfo = parseUrl(rawDevServerUrl);

export default (config, env) => {
	// (7) Compute whether SSL verification should be ignored for the proxy target
	const ignoreSSLErrors = wpTargetInfo?.isHttps && env.isDevelopment;

	// (3) Correct WebSocket scheme based on the dev-server protocol
	const wsProtocol = devServerInfo.isHttps ? "wss" : "ws";
	const devServerPort = devServerInfo.port || (devServerInfo.isHttps ? 443 : 80);

	return {
		devServer: {
			host: devServerInfo.host,
			port: devServerPort,
			hot: true,
			compress: true,
			allowedHosts: "all",
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
				logging: "info",
				overlay: {
					errors: true,
					warnings: false,
				},
				webSocketURL: `${wsProtocol}://${devServerInfo.host}:${devServerPort}/ws`,
			},
			static: {
				directory: config.paths.dist,
				publicPath: config.publicPath,
				serveIndex: false,
				watch: false,
			},
			proxy: wpTargetInfo
				? [
					{
						context: ["/"],
						target: process.env.WP_DEV_URL,
						changeOrigin: true,
						secure: !ignoreSSLErrors, // (7) inverse of ignoreSSLErrors
						headers: { "X-Webpack-Dev-Server": "true" },
					},
				]
				: undefined,
			devMiddleware: {
				publicPath: config.publicPath,
				serverSideRender: false,
			},
		},
	};
};
