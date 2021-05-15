const path = require('path');

module.exports = (key, value) => {
    if (typeof value === 'string') {
        return value;
    }
    const manifest = value;
    /**
     * Hack by https://roots.io to prepend scripts/ or styles/ to manifest keys
     *
     * This might need to be reworked at some point.
     *
     * Before:
     *   {
     *     "main.js": "scripts/main-abcdef.js"
     *     "main.css": "styles/main-abcdef.css"
     *   }
     * After:
     *   {
     *     "scripts/main.js": "scripts/main-abcdef.js"
     *     "styles/main.css": "styles/main-abcdef.css"
     *   }
     */
    Object.keys(manifest).forEach(src => {
        const sourcePath = path.basename(path.dirname(src));
        const targetPath = path.basename(path.dirname(manifest[src]));

        if ( -1 !== src.indexOf('spritemap') )
        {
            const spritemap = manifest[src].split('-')[0] + '.svg';

            // Update spritemap path
            manifest[ spritemap ] = manifest[src];

            // Remove existing
            delete manifest[ manifest[src] ];

            // Go to next one
            return;
        }
        
        if (sourcePath === targetPath) {
            return;
        }
        manifest[`${targetPath}/${src}`] = manifest[src];
        delete manifest[src];
    });
    return manifest;
};
