/**
 * @file
 */

module.exports = async(page, scenario, vp) => {
  await require('./onReadyWaitForFrames')(page, scenario, vp);
  await require('./onReadyWaitForImages')(page, scenario, vp);
  await require('./clickAndHoverHelper')(page, scenario, vp);
  await require('./onReadyInfo')(page, scenario, vp);
  await require('./performanceLog')(page, scenario, vp);
};
