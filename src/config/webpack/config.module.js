/**
 * Webpack module rules configuration
 * Uses modular loader configurations from loaders directory
 */
import preLoaders from './loaders/pre.js';
import scriptLoaders from './loaders/scripts.js';
import styleLoaders from './loaders/styles.js';
import assetLoaders from './loaders/assets.js';

export default (config, env) => {
    return {
        rules: [
            preLoaders(config, env),
            scriptLoaders(config, env),
            styleLoaders(config, env),
            assetLoaders(config, env),
        ],
    };
};
