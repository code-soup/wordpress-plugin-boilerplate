/**
 * Path utilities for webpack configuration
 * Centralizes all path operations and constants
 */

import path from 'path';

const rootPath = process.cwd();

// Main paths that are used throughout the configuration
export const paths = {
    root: rootPath,
    src: path.join(rootPath, 'src'),
    dist: path.join(rootPath, 'dist'),
    nodeModules: path.join(rootPath, 'node_modules'),
    cache: path.join(rootPath, 'node_modules/.cache/webpack'),
    icons: path.join(rootPath, 'src/icons'),
    scripts: path.join(rootPath, 'src/scripts'),
    styles: path.join(rootPath, 'src/styles'),
    images: path.join(rootPath, 'src/images'),
    config: path.join(rootPath, 'src/config'),
    templates: path.join(rootPath, 'templates'),
    includes: path.join(rootPath, 'includes'),
};

/**
 * Enhanced path resolution utility
 */

// Path resolution helpers
export const fromRoot = (...segments) => path.join(rootPath, ...segments);
export const fromSrc = (...segments) => path.join(paths.src, ...segments);
export const fromDist = (...segments) => path.join(paths.dist, ...segments);
export const fromConfig = (...segments) => path.join(paths.config, ...segments);

// Relative path from one location to another
export const relative = (from, to) => path.relative(from, to);

// Asset path helpers
export const getAssetPath = (type, filename) => `${type}/${filename}`;
export const getScriptPath = (filename) => `scripts/${filename}`;
export const getStylePath = (filename) => `styles/${filename}`;
export const getImagePath = (filename) => `images/${filename}`; 