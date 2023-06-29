const path = require('path');
const { argv } = require('yargs');
const isProduction = 'production' === argv.mode;
const rootPath = process.cwd();

/**
 * Base config
 */
module.exports = {
    mode: isProduction ? 'production' : 'development',
    fileName: isProduction ? '[name]-[fullhash:8]' : '[name]',
    paths: {
        root: rootPath,
        src: path.join(rootPath, 'src'),
        path: path.join(rootPath, 'dist'),
        publicPath: '/wp-content/plugins/',
    },
    enabled: {
        watcher: argv.watch,
        production: isProduction,
    },
};
