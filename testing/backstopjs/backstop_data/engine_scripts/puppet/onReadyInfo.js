/**
 * @file
 */

const log = require('./log');
module.exports = async(page, scenario, vp) => {
  log(`SCENARIO > ${scenario.label} VIEWPORT > ${vp.label}`, 'green');
  if (scenario._comment) {
    log(` > ${scenario._comment}`, 'green');
  }
}
