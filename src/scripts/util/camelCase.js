/**
 * Convert any string to camelCase
 */
export default str => {
    return str.replace(/_/g, " ").replace(/\W+(.)/g, function(match, chr) {
        return chr.toUpperCase();
    });
}