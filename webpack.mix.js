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

// mix.js('resources/js/app.js', 'public/js')
//     .postCss('resources/css/app.css', 'public/css', [
//         //
//     ]);


mix.scripts([
    'node_modules/jquery/dist/jquery.min.js',
    'resources/views/assets/js/jquery-ui/jquery-ui/jquery-ui.min.js',
    'node_modules/bootstrap/dist/js/bootstrap.bundle.min.js',
    'resources/views/assets/js/jquery.form.js'
], 'public/assets/js/vendor.js')
.scripts([
    'resources/views/assets/js/scripts.js'
], 'public/assets/js/scripts.js')
.scripts([
    'node_modules/jquery/dist/jquery.min.js',
    'resources/views/assets/js/jquery-ui/jquery-ui/jquery-ui.min.js',
    'resources/views/assets/js/jquery.form.js'
], 'public/assets/js/vendor-login.js')
.scripts([
    'resources/views/assets/js/login.js'
], 'public/assets/js/login.js')
.js('resources/views/assets/js/laravel-echo.js', 'public/assets/js')
.copyDirectory('resources/views/assets/js/jquery-ui/jquery-ui', 'public/assets/js/jquery-ui')
.copyDirectory('resources/views/assets/fonts', 'public/assets/fonts')
.copyDirectory('resources/views/assets/images', 'public/assets/images')
.sass('resources/views/assets/sass/bootstrap.scss', 'public/assets/css')
.sass('resources/views/assets/sass/style.scss', 'public/assets/css')
.sass('resources/views/assets/sass/login.scss', 'public/assets/css')
.options({
    processCssUrls: false,
})
.version();