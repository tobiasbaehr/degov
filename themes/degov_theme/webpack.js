const path = require('path');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

module.exports = {
  entry: [
    // deGov theme
    './scss/main.scss',
    // Bootstrap
    './node_modules/bootstrap/js/src/carousel.js',
    './node_modules/bootstrap/scss/bootstrap-grid.scss',
    // Octicons
    './node_modules/octicons/index.js',
    './node_modules/octicons/index.scss',
  ],
  plugins: [
    new MiniCssExtractPlugin({
      // Options similar to the same options in webpackOptions.output
      // both options are optional
      filename: "[name].css",
      chunkFilename: "[id].css"
    })
  ],
  output: {
    path: path.join(__dirname, '/webpack-dist/'),
    filename: 'bundle.js'
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: [/(node_modules)/],
        use: [{
          loader: 'babel-loader',
          options: {
            babelrc: './.babelrc'
          }
        }]
      },
      {
        test: /\.scss$/,
        use: [
          MiniCssExtractPlugin.loader,
          { loader: 'css-loader', options: { url: false, sourceMap: true } },
          { loader: 'sass-loader', options: { sourceMap: true } }
        ],
      }
    ]
  }
};