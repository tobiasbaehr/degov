# deGov Theme

## Assets packaging

We are using the [Webpack](https://webpack.js.org/) JS/CSS module bundler. Find its configuration in the `webpack.js`
file.

## NPM packages
Run `npm install` to get the dependencies.

## Compile your assets (JS and CSS files)

### Development

You must disable CSS and JS aggregation in your Drupal backend, if you want to compile the assets in development mode.
Enabled CSS and JS aggregation in Drupal works only, if you are compiling the assets in production mode.

`npm run-script build`

### Production
`npm run-script build:prod`

## Iconfonts

We are using the Font Awesome free package. See icons palette [here](https://fontawesome.com/icons?d=gallery&m=free).