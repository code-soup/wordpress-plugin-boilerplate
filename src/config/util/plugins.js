/**
 * Plugin utilities for webpack configuration
 * Helps manage conditional plugin loading and configuration
 */

/**
 * Loads plugins based on conditions
 * Each item should have a condition (boolean) and factory (function that returns a plugin instance)
 * 
 * @param {Array<{condition: boolean, factory: Function}>} pluginConfigs - Array of plugin configurations
 * @return {Array} Array of instantiated plugins
 */
const conditionalPlugins = (pluginConfigs) => {
    return pluginConfigs
        .filter(({ condition }) => condition)
        .map(({ factory }) => factory());
};

/**
 * Lazy-loads a plugin only when needed
 * This helps reduce initial memory usage and improves startup time
 * 
 * @param {string} pluginModule - The module name to require
 * @param {Function} configFn - Function that receives the plugin constructor and returns an instance
 * @return {Object} The instantiated plugin
 */
const lazyLoadPlugin = (pluginModule, configFn) => {
    const Plugin = require(pluginModule);
    
    // Handle both direct exports and named exports
    const Constructor = Plugin.default || Plugin;
    
    return configFn(Constructor);
};

module.exports = {
    conditionalPlugins,
    lazyLoadPlugin
}; 