const path = require('path');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CopyWebpackPlugin = require('copy-webpack-plugin');

module.exports = {
  entry: [
    // deGov theme
    './scss/main.scss',
    // Bootstrap
    './node_modules/bootstrap/js/src/carousel.js',
    './node_modules/bootstrap/scss/bootstrap-grid.scss',
    // Font Awesome
    './node_modules/@fortawesome/fontawesome-free/js/all.js',
    './node_modules/@fortawesome/fontawesome-free/css/all.css',
  ],
  plugins: [
    new MiniCssExtractPlugin({
      // Options similar to the same options in webpackOptions.output
      // both options are optional
      filename: "[name].css",
      chunkFilename: "[id].css"
    }),
    new CopyWebpackPlugin([{
      context: `${__dirname}/node_modules/@fortawesome/fontawesome-free/webfonts`,
      from: '*',
      to: `${__dirname}/webfonts/`
    }])
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
        test: [/\.scss$/, /\.css$/],
        use: [
          MiniCssExtractPlugin.loader,
          { loader: 'css-loader', options: { url: false, sourceMap: true } },
          { loader: 'sass-loader', options: { sourceMap: true } }
        ],
      }
    ]
  }
};