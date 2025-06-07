/**
 * Plugin utilities for webpack configuration
 * Helps manage conditional plugin loading and configuration.
 */

/**
 * Filters and instantiates plugins based on a condition.
 *
 * @param {Array<{condition: boolean, factory: Function}>} pluginConfigs - Array of plugin configurations.
 * @return {Array} Array of instantiated plugins that meet the condition.
 */
export const conditionalPlugins = (pluginConfigs) => {
    return pluginConfigs
        .filter(({ condition }) => !!condition)
        .map(({ factory }) => factory());
};

/**
 * A wrapper to configure a plugin.
 * This is primarily used to keep plugin-specific logic clean in the main config.
 *
 * @param {Object} Plugin - The imported plugin constructor.
 * @param {Function} configFn - A function that receives the plugin constructor and returns a configured plugin instance.
 * @return {Object} The configured plugin instance.
 */
export const configurePlugin = (Plugin, configFn) => {
    // Handle both direct exports and named exports
    const Constructor = Plugin.default || Plugin;
    return configFn(Constructor);
};