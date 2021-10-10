module.exports = {
    minimize: true,
    emitOnErrors: false,
    mangleWasmImports: true,
    splitChunks: {
        chunks: 'async',
        minSize: 20000,
        minRemainingSize: 0,
        minChunks: 1,
    },
};
