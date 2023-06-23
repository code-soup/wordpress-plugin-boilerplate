/**
 * @export
 * @param {dir} directory path
 * @return {any}
 */
module.exports = dir => {
    const path = require("path");

    return path.join(__dirname, "..", dir);
};
