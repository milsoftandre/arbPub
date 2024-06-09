const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('publick/assets/plugins/global/plugins.bundle1.js', 'public/js');
mix.js('assets/plugins/global/plugins.bundle.js', 'public/js')
    .postCss('assets/plugins/global/plugins.bundle.css', 'public/css', [
        //
    ]);
