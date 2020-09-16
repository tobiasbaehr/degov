/**
 * @file
 */

const log = require('./log');

module.exports = async(page, scenario, vp) => {

  await require('./loadCookies')(page, scenario, vp);

  if (scenario.disableJavascript) {
    await page.setJavaScriptEnabled(false);
  }

  // Browser console.
  const verbose = false;
  page
    .on('pageerror', ({ message }) => log(message, 'red'))
    .on('workercreated', ({ message }) => console.log(message))
    .on('requestfailed', request => log(`${request.failure().errorText} ${request.url()}`), 'red')
    .on('console', message => {
      if (message.type() === 'error') {
        log(`${message.type().toUpperCase()} ${message.text()}`, 'red')
      }
      if (message.type() !== 'error' && verbose) {
        if (message.type() === 'info') {
          log(`${message.type().toUpperCase()} ${message.text()}`, 'green')
        }
        else {
          log(`${message.type().toUpperCase()} ${message.text()}`)
        }
      }
    })
    .on('response', response => {
      if (verbose) {
        if (response.status() === 200 || response.status() === 206) {
          log(`${response.status()} ${response.url()}`, 'gray');
        }
        else if (response.status() < 400) {
          log(`${response.status()} ${response.url()}`, 'green');
        }
      }
      if (response.status() >= 400) {
        log(`${response.status()} ${response.url()}`, 'red');
      }
    });

  await page.setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36')
  await page._client.send('Performance.enable');
  await page._client.send('Performance.getMetrics');
};
