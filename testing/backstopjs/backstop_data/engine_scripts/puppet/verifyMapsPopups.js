/* jshint esversion: 8, node: true */


module.exports = async (page, scenario, vp, isReference, pupeteer) => {
  var postInteractionWait = 1000; // scenario.postInteractionWait;

  await require('./onReadyInfo')(page, scenario, vp);
  await require('./onReadyWaitForImages')(page, scenario, vp);
  await require('./clickAndHoverHelper')(page, scenario, vp);

  // Dismiss API Key messages.
  await page.waitFor(1000);
  await page.evaluate(async() => {
    jQuery(".normal-page__content-paragraphs>div:nth-child(2) .styled-google-map .dismissButton").trigger('click');
  });
  await page.evaluate(async() => {
    jQuery(".normal-page__content-paragraphs>div:nth-child(4) .styled-google-map .dismissButton").trigger('click');
  });
  await page.waitFor(1000);

  await page.evaluate(async() => {
    await jQuery(jQuery('.leaflet-marker-icon')[0]).trigger('click');
  });
  await page.evaluate(async() => {
    await jQuery('#' + jQuery('.styled-google-map')[0].id).trigger('clickFirstMarker');
  });
  await page.waitFor(1000);

  if (postInteractionWait) {
    await page.waitFor(postInteractionWait);
  }
};
