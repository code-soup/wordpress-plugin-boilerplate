module.exports = {
    entry: {
        main: [
            "scripts/main.js",
            "styles/main.scss"
        ],
        admin: [
            "scripts/admin.js",
            "styles/admin.scss",
        ],
    },
    // open browser with devUrl url when watch mode starts
    openBrowserOnWatch: false,
    // show webpack compailing errors in browser while in watch mode
    showErrorsInBrowser: true,
    // use https: in devUrl
    useSSLinDev: false,
    // path to plugin folder
    publicPath: "/wp-content/plugins/wppb",
    // localhost or custom host name
    devUrl: "http://mylocal.domain",
    // proxyUrl, when watch mode is enabled you will access it on this url
    proxyUrl: "http://localhost",
    // proxy port used in watch mode
    proxyPort: 3000,
    // folders / files to include in while in watch mode to monitor for changes
    // change in css/js is automatically included
    watch: ["includes/**/*.php"],
};