const path = require('path');
const dotenv = require('dotenv');
const rootPath = process.cwd();
const isProduction = -1 === process.argv.indexOf('development');
const pluginDirName = path.basename(path.join(__dirname, '../..'));

dotenv.config({
    path: path.join(rootPath, '.env.local'),
});

/**
 * Auto-set publich path or read from .env
 */
const publicPath =
    'undefined' === typeof process.env.WP_PUBLIC_PATH
        ? `${process.env.WP_CONTENT_PATH}/${pluginDirName}/dist/`
        : process.env.WP_PUBLIC_PATH;

/**
 * Base config
 */
module.exports = {
    mode: isProduction ? 'production' : 'development',
    paths: {
        root: rootPath,
        src: path.join(rootPath, 'src'),
        dist: path.join(rootPath, 'dist'),
        publicPath: publicPath,
    },
    enabled: {
        watcher: -1 !== process.argv.indexOf('serve'),
        production: isProduction,
    },
    fileName: isProduction ? "[name]-[fullhash]" : "[name]",
};
