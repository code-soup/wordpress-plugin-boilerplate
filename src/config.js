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
    // inline images smaller than 16kb
    // this will base64 encode images inside of CSS file that are smaller than 16kb
    imageSizeInline: 16,
    // open browser with devUrl url when watch mode starts
    openBrowserOnWatch: false,
    // show webpack compailing errors in browser while in watch mode
    showErrorsInBrowser: true,
    // use https: in devUrl
    useSSLinDev: false,
    // URL to root folder on web server
    // This is very important line, most common reason why things break
    // because paths to files are determined by this
    publicPath: "/wp-content/plugins/wordpress-plugin-boilerplate",
    // production path to /dist folder, if different from 
    publicPathProd: "/wp-content/plugins/wordpress-plugin-boilerplate",
    // localhost or custom host name
    devHost: "http://localhost",
    // Use webpack-dev-server built-in proxy server
    useProxy: false,
    // proxyUrl, when watch mode is enabled you will access website on this url
    proxyHost: "http://localhost",
    // proxy port used in watch mode
    proxyPort: 3000,
    // folders / files to include in while in watch mode to monitor for changes
    // change in css/js is automatically included
    watch: [
        "includes/**/*.php",
        "templates/**/*.php",
    ],
};