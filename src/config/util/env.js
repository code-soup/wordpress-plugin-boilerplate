/**
 * Environment utilities
 * Provides environment-specific constants and functions, evaluated once.
 */
import fs from 'fs';
import { paths } from './paths.js';

// Environment constants - evaluated once and cached.
export const isProduction = process.env.NODE_ENV === 'production';
export const isDevelopment = process.env.NODE_ENV === 'development';
export const isWatching = !!process.env.WEBPACK_SERVE;
export const isAnalyzing = !!process.env.ANALYZE;
export const isLintingScripts = !!process.env.LINT_SCRIPTS;
export const isLintingStyles = !!process.env.LINT_STYLES;

/**
 * Returns a value based on the current environment.
 * @param {*} prodValue - Value for production.
 * @param {*} devValue - Value for development.
 * @return {*} The environment-specific value.
 */
export const getEnvSpecific = (prodValue, devValue) =>
    isProduction ? prodValue : devValue;

/**
 * Memoized function to check for SVG icons.
 * This ensures the file system is checked only once.
 */
const checkHasSvgIcons = (() => {
    let hasIcons;
    return () => {
        if (hasIcons === undefined) {
            try {
                hasIcons =
                    fs.existsSync(paths.icons) &&
                    fs.readdirSync(paths.icons).some((file) => file.endsWith('.svg'));
            } catch (e) {
                console.error('Error checking for SVG icons:', e);
                hasIcons = false;
            }
        }
        return hasIcons;
    };
})();

export const hasSvgIcons = checkHasSvgIcons(); 