/**
 * @file
 */

module.exports = async(page, scenario, vp) => {
  await Promise.all(page.frames().map((f) => f.waitForNavigation));
};
