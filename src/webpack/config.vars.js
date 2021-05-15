// No need to edit this file unless something is wrong
// This will generate correct variables for webpack
const desire   = require('./utils/desire');
const resolver = require('./utils/resolve');

const path      = require('path');
const { argv }  = require('yargs');
const { merge } = require('webpack-merge');

// Use local or default config
const userConfig   = desire( resolver('config-local.js'), resolver('config.js') );
const isProduction = ('production' === argv.mode);

/**
 * Base config
 */
const config = merge(
    {
        mode: isProduction ? 'production' : 'development',
        fileName: isProduction ? '[name]-[fullhash:9]' : '[name]',
        assetFilename: isProduction ? '[path][name]-[hash:9][ext]' : '[path][name][ext]',
        paths: {
            src: resolver(),
            root: resolver('../'),
            dist: resolver('../dist'),
            publicPath: path.join(userConfig.publicPath, 'dist/'),
            publicPathProd: path.join(userConfig.publicPathProd, 'dist/'),
            node_modules: resolver('../node_modules'),
        },
        enabled: {
            watcher: ( -1 !== argv['_'].indexOf('serve') ),
            production: isProduction,
        },
    },
    userConfig
);

// Export config
module.exports = config;