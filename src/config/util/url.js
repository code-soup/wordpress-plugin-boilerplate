/**
 * URL parsing utilities for webpack configuration
 */

import { URL } from "url";

/**
 * Parse a URL and return its components in a convenient shape.
 * Exits the process on invalid input so that mis-configured .env files
 * fail fast instead of silently falling back to defaults.
 *
 * @param {string} value        The URL to parse.
 * @param {() => object} [fallback] Function producing a fallback return value.
 * @returns {{ host: string, port: number|undefined, protocol: string, isHttps: boolean, url: URL }}
 */
export const parseUrl = (value, fallback) => {
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