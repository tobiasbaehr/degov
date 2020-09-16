/**
 * @file
 */

const log = require('./log')

module.exports = async(page, scenario, vp) => {

  // Check https://www.html5rocks.com/en/tutorials/webperformance/basics/
  // It includes a diagramm visualizing these values
  // https://i.stack.imgur.com/qBvJL.png
  const metricsWhitelist = [
    'requestStart',
    'responseStart',
    'responseEnd',
    'domInteractive',
    'domComplete',
    'loadEventStart',
    'loadEventEnd',
    'duration'
  ];

  // Todo: Add log level after https://github.com/garris/BackstopJS/pull/822.
  const verbose = false;

  const formatNumber = (val) => {
    const digits = 2;

    // Only format floats float.
    if (Number(val) === val && val % 1 !== 0) {
      if (val > 1000) {
        return `${Number.parseFloat(val / 1000).toFixed(digits)}s`
      }
      return `${Number.parseFloat(val).toFixed(digits)}ms`
    }
    // Format sizes.
    if (Number.isInteger(val)) {
      if (val === 0) {
return '0 Bytes';
      }
      const k = 1024;
      const dm = digits < 0 ? 0 : digits;
      const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
      const i = Math.floor(Math.log(val) / Math.log(k));
      return parseFloat((val / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }
    return val;
  }

  // Getting performance data for all requests in page.
  const performanceData = JSON.parse(
    await page.evaluate(() => JSON.stringify(window.performance.getEntries()))
  );

  // Transfer size.
  let sizes = {
    html: 0,
    images: 0,
    css: 0,
    cssImage: 0,
    js: 0,
    fonts: 0,
    total: 0,
  };
  performanceData.forEach((request) => {
    if (request.transferSize) {
      sizes.total += request.transferSize;

      if (request.initiatorType === 'navigation') {
        sizes.html += request.transferSize;
      }
      if (request.initiatorType === 'img') {
        sizes.images += request.transferSize;
      }
      else if (request.initiatorType === 'script') {
        sizes.js += request.transferSize;
      }
      else if (request.initiatorType === 'css' || request.initiatorType === 'link') {
        // Remove GET suffixes.
        let url = request.name;
        if (url.lastIndexOf('?') > 0) {
          url = url.substring(0, url.lastIndexOf('?'));
        }
        const cssFile = /.\.css$/;
        const fontFile = /.\.woff[2]?$/;

        if (cssFile.test(url)) {
          sizes.css += request.transferSize;
        }
        else if (fontFile.test(url)) {
          sizes.fonts += request.transferSize;
        }
        else if ((/\.(gif|jpg|jpeg|tiff|png)$/i).test(url)) {
          sizes.cssImage += request.transferSize;
        }
      }

    }
  });

  // Evaluate page.url() request.
  const thisPageHtmlRequest = performanceData.filter((elm) => elm.name === page.url())[0]

  if (verbose && metricsWhitelist.length) {
    log(' Page performance metrics', 'green')
    const whitelisted = Object.assign({}, ...metricsWhitelist.map(key => ({ [key]: formatNumber(thisPageHtmlRequest[key]) })))
    for (let [key, value] of Object.entries(whitelisted)) {
      const space = 16 - key.length;
      log(`  ${key}: ${' '.repeat(space)}${value}`, 'gray');
    }
  }

  // First paint.
  const firstPaint = performanceData.filter((elm) => elm.name === 'first-paint')[0];
  const color = firstPaint.startTime < 1000 ? 'green' : 'red';

  if (verbose) {
    log(`  First paint       ${formatNumber(firstPaint.startTime)}`, color);
    log(`  Document ready    ${formatNumber(thisPageHtmlRequest.loadEventEnd)}`, color);

    // Aggregated request size.
    const checksum = sizes.total - (sizes.js + sizes.fonts + sizes.css + sizes.images + sizes.cssImage + sizes.html);

    log(` Request content size`, 'green');
    log(`  HTML           ${formatNumber(sizes.html)} (document size)          `, 'gray');
    log(`  Images         ${formatNumber(sizes.images)} (content images)          `, 'gray');
    log(`  CSS            ${formatNumber(sizes.css)}                              `, 'gray');
    log(`  fonts          ${formatNumber(sizes.fonts)}                            `, 'gray');
    log(`  Javascript     ${formatNumber(sizes.js)}`, 'gray');
    log(`  CSS Images     ${formatNumber(sizes.cssImage)} (imaged loaded via CSS)`, 'gray');
    log(`  uncategorized  ${formatNumber(checksum)}                              `, 'gray');
    log(`  total          ${formatNumber(sizes.total)}                            `, 'green');
  }
  else {
    log(`   response: ${formatNumber(firstPaint.startTime)} done: ${formatNumber(thisPageHtmlRequest.loadEventEnd)} size: ${formatNumber(sizes.total)}`);
  }
};
