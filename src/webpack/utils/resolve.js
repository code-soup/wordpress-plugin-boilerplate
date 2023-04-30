/**
 * Returns path from ./src directory to any sub-directory
 * 
 * @export
 * @param {dir} sub-directory path
 * @return {string}
 */
module.exports = dir => {

    const path = require('path');

    // Path from this file to ./src
    // We will use /src as a root folder
    const rootPath = path.join(__dirname, '/../..');

    // dir not specified, return root
    if ( 'undefined' === typeof dir )
    {
        return rootPath;
    }

    // Return path to dir
    return path.join(rootPath, dir);
};
