const path = require('path');
const fs = require('fs');
const $ = require("jquery");
const webpack = require('webpack');
module.exports = {
  entry: ['babel-polyfill','./src/drupal-behavior-function/node-form-client.js', 'jquery'],
  output: {
    path: path.join(__dirname, '/webpack-dist/'),
    filename: 'bundle.js'
  },
  plugins: [
    new webpack.ProvidePlugin({
      $: 'jquery',
      jQuery: 'jquery'
    })
  ],
  module: {
    rules: [
      {
        exclude: /(node_modules)/,
        use: [{
          loader: 'babel-loader',
          options: JSON.parse(fs.readFileSync(path.resolve(__dirname, '.babelrc'))),
          }
        ]
      }
    ]
  },
  devtool: 'none',
};

