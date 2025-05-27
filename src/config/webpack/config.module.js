/**
 * Webpack module rules configuration
 * Uses modular loader configurations from loaders directory
 */

module.exports = (config, env) => {
    // Import all loader configurations
    const preLoaders = require('./loaders/pre')(config, env);
    const scriptLoaders = require('./loaders/scripts')(config, env);
    const styleLoaders = require('./loaders/styles')(config, env);
    const assetLoaders = require('./loaders/assets')(config, env);
    
    return {
        rules: [
            preLoaders,
            scriptLoaders,
            styleLoaders,
            assetLoaders,
        ],
    };
};
