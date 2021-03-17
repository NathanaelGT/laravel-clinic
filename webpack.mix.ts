const mix = require('laravel-mix')

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

mix.sass('resources/scss/app.scss', 'public/css')
   .js('resources/ts/jsx-render.ts', 'public/js')
   .js('resources/ts/home.tsx', 'public/js')
   .js('resources/ts/patientAppointment.tsx', 'public/js')
   .js('resources/ts/admin/newService.ts', 'public/js/admin')
   .js('resources/ts/admin/doctorList.ts', 'public/js/admin')
   .sourceMaps(false, 'source-map')
   .disableNotifications()
   .webpackConfig({
      resolve: {
         extensions: ['.ts', '.tsx']
      },
      module: {
         rules: [
            {
               test: /\.tsx?$/,
               loader: 'babel-loader',
               exclude: /node_modules/
            }
         ]
      }
   })

if (mix.inProduction()) mix.version()
