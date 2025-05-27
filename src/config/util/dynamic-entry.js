const fs = require('fs');
const path = require('path');
const entryDir = path.join(process.cwd(), 'src', 'entry');

// Note: Entry files use .js extension but contain JSON content for consistency with the project structure

// Cache for development mode
const entryCache = {};

/**
 * Validate entry point structure
 * @param {any} entry - The entry content to validate
 * @param {string} filePath - Path to the entry file
 * @return {boolean} Whether the entry is valid
 */
function validateEntryPoint(entry, filePath) {
    if (!Array.isArray(entry)) {
        console.warn(`Warning: entry in ${filePath} should be an array.`);
        return false;
    }
    
    // Check that all entries are strings
    if (!entry.every(item => typeof item === 'string')) {
        console.warn(`Warning: all items in entry array in ${filePath} should be strings.`);
        return false;
    }
    
    return true;
}

/**
 * Read entry files from directories and parse them
 * @param {string} dir - Directory to read
 * @param {object} result - Accumulated result
 * @return {object} Map of entry points
 */
function readJsonFiles(dir, result = {}) {
    try {
        if (!fs.existsSync(dir)) {
            console.warn(`Entry directory ${dir} does not exist.`);
            return result;
        }
        
        const files = fs.readdirSync(dir);
        
        for (const file of files) {
            try {
                const filePath = path.join(dir, file);
                const stats = fs.statSync(filePath);
                
                if (stats.isDirectory()) {
                    result[file] = readJsonFiles(filePath, {});
                } else if (stats.isFile() && path.extname(file) === '.js') {
                    const fileContent = fs.readFileSync(filePath, 'utf8');
                    
                    // Skip empty files with warning
                    if (!fileContent.trim()) {
                        console.warn(`Warning: Entry file ${filePath} is empty. Skipping.`);
                        continue;
                    }
                    
                    try {
                        const data = JSON.parse(fileContent);
                        
                        // Skip files with missing or invalid entry array
                        if (!data.entry) {
                            console.warn(`Warning: Entry file ${filePath} is missing "entry" property. Skipping.`);
                            continue;
                        }
                        
                        const folderName = path.basename(dir);
                        const fileName = path.parse(file).name;
                        const key = 'common' !== fileName ? `${folderName}-${fileName}` : folderName;
                        
                        // Validate entry structure
                        if (validateEntryPoint(data.entry, filePath)) {
                            result[key] = data.entry;
                        }
                    } catch (jsonError) {
                        console.warn(`Warning: Error parsing JSON in ${filePath}: ${jsonError.message}. Skipping.`);
                    }
                }
            } catch (fileError) {
                console.error(`Error processing file ${file}: ${fileError.message}`);
            }
        }
        
        return result;
    } catch (dirError) {
        console.error(`Error reading directory ${dir}: ${dirError.message}`);
        return result;
    }
}

/**
 * Get dynamic entry points, with caching in development mode
 * @return {object} Formatted entry points
 */
function getDynamicEntries() {
    const isDev = process.env.NODE_ENV !== 'production';
    
    // In development, use cached entries if available
    if (isDev && entryCache.entries) {
        return entryCache.entries;
    }
    
    const entries = readJsonFiles(entryDir);
    const entryValues = Object.values(entries);
    
    // Safely handle empty values array
    const formatted = entryValues.length > 0 
        ? Object.assign({}, ...entryValues) 
        : {};
    
    // Cache the result for subsequent builds in watch mode
    entryCache.entries = formatted;
    
    return formatted;
}

// Export the function instead of immediately calling it
module.exports = getDynamicEntries;
