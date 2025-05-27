/**
 * Path utilities for webpack configuration
 * Centralizes all path operations and constants
 */

const path = require('path');
const rootPath = process.cwd();

// Main paths that are used throughout the configuration
const paths = {
    root: rootPath,
    src: path.join(rootPath, 'src'),
    dist: path.join(rootPath, 'dist'),
    nodeModules: path.join(rootPath, 'node_modules'),
    cache: path.join(rootPath, 'node_modules/.cache/webpack'),
    entry: path.join(rootPath, 'src/entry'),
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
module.exports = {
    // Main path constants
    paths,

    // Path resolution helpers
    fromRoot: (...segments) => path.join(rootPath, ...segments),
    fromSrc: (...segments) => path.join(paths.src, ...segments),
    fromDist: (...segments) => path.join(paths.dist, ...segments),
    fromConfig: (...segments) => path.join(paths.config, ...segments),
    
    // Relative path from one location to another
    relative: (from, to) => path.relative(from, to),
    
    // Asset path helpers
    getAssetPath: (type, filename) => `${type}/${filename}`,
    getScriptPath: (filename) => `scripts/${filename}`,
    getStylePath: (filename) => `styles/${filename}`,
    getImagePath: (filename) => `images/${filename}`,
    
    // Path resolver for aliases (extends the original resolver utility)
    resolve: (dir) => {
        if (dir.startsWith('@')) {
            // Handle alias-style paths
            const aliasMap = {
                '@utils': path.join(paths.scripts, 'util'),
                '@styles': paths.styles,
                '@scripts': paths.scripts,
                '@icons': paths.icons,
                '@images': paths.images,
            };
            
            const [alias, ...segments] = dir.split('/');
            if (aliasMap[alias]) {
                return path.join(aliasMap[alias], ...segments);
            }
        }
        
        // Default to regular path resolution
        return path.join(paths.config, dir);
    }
}; 