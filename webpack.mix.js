const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
  .vue({ version: 3 })
  .sass('resources/sass/app.scss', 'public/css')
  .options({ processCssUrls: false })
  .webpackConfig({
    output: { chunkFilename: 'js/[name].js?id=[chunkhash]' },
  })
  .disableNotifications();
