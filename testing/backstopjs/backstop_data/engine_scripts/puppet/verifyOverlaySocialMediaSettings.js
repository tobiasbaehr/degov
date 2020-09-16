/**
 * @file
 */

module.exports = async(page, scenario, vp) => {
  await require('./onReadyInfo')(page, scenario, vp);
  await require('./clickAndHoverHelper')(page, scenario, vp);
  await require('./onReadyWaitForImages')(page, scenario, vp);

  await page.waitForSelector(
    "div[data-social-media-source='instagram'] a[data-target='#social-media-settings']", {
      timeout: 15000
    }
  );
  await page.evaluate(() => document.querySelector("div[data-social-media-source='instagram'] a[data-target='#social-media-settings']").click());
  await new Promise(resolve => setTimeout(resolve, 300));
  await page.click(`#checkbox-twitter`);
  await new Promise(resolve => setTimeout(resolve, 300));
};
