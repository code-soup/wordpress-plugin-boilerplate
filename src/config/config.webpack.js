/**
 * Main webpack configuration entry point
 * Uses the factory pattern for creating the configuration
 */

// Import factory function
const createWebpackConfig = require('./factory');

// Create and export the webpack configuration
module.exports = createWebpackConfig();
