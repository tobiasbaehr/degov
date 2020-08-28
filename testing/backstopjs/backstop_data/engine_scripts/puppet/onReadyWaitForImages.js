/**
 * @file
 */

module.exports = async(page, scenario, vp) => {

  // Wait for images to be loaded.
  // See: https://stackoverflow.com/a/49233383/308533
  await page.evaluate(async() => {
    const selectors = Array.from(document.querySelectorAll("img"));
    await Promise.all(selectors.map(img => {
      if (img.complete) {
return;
      }
      return new Promise((resolve, reject) => {
        img.addEventListener('load', resolve);
        img.addEventListener('error', reject);
      });
    }));
  });

}
