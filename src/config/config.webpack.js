/**
 * Main webpack configuration entry point
 * Uses the factory pattern for creating the configuration
 */

// Import factory function
import createWebpackConfig from './factory.js';

// Create and export the webpack configuration
export default createWebpackConfig();
