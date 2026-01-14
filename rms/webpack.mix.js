const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
  .vue({ version: 3 })
  .sass('resources/sass/app.scss', 'public/css')
  .options({ processCssUrls: false })
  .webpackConfig({
    output: { chunkFilename: 'js/[name].js?id=[chunkhash]' },
  })
  .disableNotifications();

const webpack = require('webpack');
mix.webpackConfig({
  plugins: [
    new webpack.DefinePlugin({
      __VUE_OPTIONS_API__: JSON.stringify(true),
      __VUE_PROD_DEVTOOLS__: JSON.stringify(false),
      __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: JSON.stringify(false),
    }),
  ],
});
