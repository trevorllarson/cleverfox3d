let mix = require("laravel-mix");
require("laravel-mix-purgecss");
require("dotenv").config();
const path = require("path");
const fs = require('fs');
const THEME_PATH = process.env.MIX_PURGECSS_THEME_PATH;
let purgecssWordpress = require("purgecss-with-wordpress");

mix.webpackConfig({
    watchOptions: { ignored: /node_modules/ },
    externals: { jquery: "jQuery" },
    resolve: {
        extensions: ['.scss']
    }
});

function getFiles(dir) {
    return fs.readdirSync(dir).filter(file => {
        return file.charAt(0) !== '.';
    });
}

const blockFolders = getFiles(THEME_PATH + '/blocks/');

for (const blockFolder of blockFolders) {
    if(blockFolder === 'components') continue;
    mix.setPublicPath(THEME_PATH)
        .sass(`${THEME_PATH}/blocks/${blockFolder}/${blockFolder}.scss`, `${THEME_PATH}/assets/css/${blockFolder}.css`)
        .version();
}

mix.setPublicPath(THEME_PATH)
    .sass("assets/scss/theme.scss", "assets/css/theme.css")
    .sass("assets/scss/editor.scss", "assets/css/editor.css")
    .purgeCss({
        enabled: false,
        extend: {
            content: [
                path.join(
                    __dirname,
                    path.join(process.env.MIX_PURGECSS_THEME_PATH, "/**/*.php")
                ),
                "assets/js/**/*.js",
                path.join(
                    __dirname,
                    path.join(
                        process.env.MIX_PURGECSS_THEME_PATH,
                        "acf-json/**/*.json"
                    )
                ),
                "src/wp-content/plugins/**/*.php",
                "src/wp-content/plugins/**/*.js",
                "src/wp-includes/js/dist/**/*.js",
            ],
            safelist: [
                ...purgecssWordpress.safelist,
                /g-,*/,
                /gform.*/,
                /bg-.*/,
                /swiper-.*/,
                /slider-.*/,
                /#*/,
            ],
        },
    })
    .js("assets/js/theme.js", "assets/js/theme.js")
    .js("assets/js/editor.js", "assets/js/editor.js")
    .version()
    .sourceMaps()
    .options({ processCssUrls: false });

if (process.env.MIX_BROWSERSYNC == "true") {
    mix.browserSync({
        proxy: "https://" + process.env.MIX_DOMAIN,
        host: process.env.MIX_DOMAIN,
        notify: false,
        files: ["./src/**/*"],
        open: "external",
        https: {
            key: process.env.MIX_CERT_PATH + process.env.MIX_DOMAIN + ".key",
            cert: process.env.MIX_CERT_PATH + process.env.MIX_DOMAIN + ".crt",
        },
    });
}
