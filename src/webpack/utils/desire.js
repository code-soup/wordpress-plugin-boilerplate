/**
 * @export
 * @param {string} dependency
 * @param {any} [fallback]
 * @return {any}
 */
module.exports = (dependency, fallback) => {

    try {
        require.resolve(dependency);
    } catch (err) {
        console.warn(`File not found. desire.js trying to include file: ${dependency}` );
    }

    return require(fallback); // eslint-disable-line import/no-dynamic-require
};
