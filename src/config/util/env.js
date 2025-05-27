/**
 * Environment detection utility
 * Centralizes all environment and mode detection logic in one place
 */

// Production detection - either explicitly set NODE_ENV or using CLI args
const isProduction = process.env.NODE_ENV === 'production' || -1 === process.argv.indexOf('development');

// Watch mode detection
const isWatching = -1 !== process.argv.indexOf('serve');

// Lint command detection (both general and specific types)
const isLinting = process.argv.some(arg => 
    arg.includes('lint') || 
    arg.includes('lint:scripts') ||
    arg.includes('lint:styles')
);
const isLintingScripts = isLinting && process.argv.some(arg => 
    arg.includes('lint:scripts') || arg === 'lint'
);
const isLintingStyles = isLinting && process.argv.some(arg => 
    arg.includes('lint:styles') || arg === 'lint'
);

// Bundle analysis detection
const isAnalyzing = process.argv.includes('--analyze') || process.env.ANALYZE === 'true';

/**
 * Environment information object
 */
module.exports = {
    // Environment modes
    isProduction,
    isDev: !isProduction,
    
    // Operation modes
    isWatching,
    isLinting,
    isLintingScripts,
    isLintingStyles,
    isAnalyzing,
    
    // Helper for getting env-specific values
    getEnvSpecific: (prodValue, devValue) => isProduction ? prodValue : devValue
}; 