/**
 * @export
 * @param {dir} directory path
 * @return {any}
 */
module.exports = (dir) => {
    const path = require('path');
    // console.log(path.join(__dirname, '..', dir));
    return path.join(__dirname, '..', dir);
};
