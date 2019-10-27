let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/assets/js/app.js', 'public/js')
   .sass('resources/assets/sass/app.scss', 'public/css');

mix.styles([
    'node_modules/fullcalendar/dist/fullcalendar.css'
], 'public/css/calender.css');

mix.styles([
    'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.css',
    'node_modules/bootstrap-timepicker/css/bootstrap-timepicker.css'
], 'public/css/datepicker.css');

mix.styles([
    'node_modules/ionicons/dist/css/ionicons.css',
    'node_modules/admin-lte/dist/css/skins/_all-skins.min.css'
], 'public/css/ionicons.css');
mix.scripts([
    'node_modules/admin-lte/dist/js/adminlte.min.js',
    'node_modules/jquery/dist/js/jquery.min.js',
    'node_modules/datatables.net/js/jquery.dataTables.js',
    'node_modules/moment/moment.js',
    'node_modules/fullcalendar/dist/fullcalendar.js',
    'node_modules/jquery-ui/ui/widgets/core.js',
    'node_modules/jquery-ui/ui/widgets/mouse.js',
    'node_modules/jquery-ui/ui/widgets/widget.js',
    'node_modules/jquery-ui/ui/widgets/draggable.js',
    'node_modules/bootstrap-datepicker/dist/js/bootstrap-datepicker.js',
    'node_modules/bootstrap-timepicker/js/bootstrap-timepicker.js'
    
], 'public/js/vendor.js');

if (mix.inProduction()) {
  mix.version();
}
