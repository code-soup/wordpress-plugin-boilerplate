const path = require("path");
const merge = require("webpack-merge");
const { argv } = require("yargs");

const desire = require("./util/desire");

// Check for local config over
const userConfig = desire(`${__dirname}/../config-local`)
    ? desire(`${__dirname}/../config-local`)
    : desire(`${__dirname}/../config`);

const isProduction = ("production" === argv.mode);
const rootPath = process.cwd();

/**
 * Base config
 */
const config = merge(
    {
        mode: isProduction ? 'production' : 'development',
        copy: "+(icons|audio)/**/*",
        fileName: isProduction ? "[name]-[hash:8]" : "[name]",
        paths: {
            root: rootPath,
            src: path.join(rootPath, "src"),
            path: path.join(rootPath, "dist"),
            publicPath: `/${userConfig.publicPath}/dist/`,
        },
        enabled: {
            watcher: argv.watch,
            production: isProduction,
        },
    },
    userConfig
);

// Export config
module.exports = config;
