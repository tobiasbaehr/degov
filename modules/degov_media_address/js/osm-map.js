/**
 * @file
 * osm-map.js
 *
 * Open Street Map for Address paragraph.
 */

(function ($, Drupal, drupalSettings) {
  'use strict';

  /**
   * Initializes the Map with Leaflet.
   */
  Drupal.behaviors.osmMap = {
    attach: function (context, drupalSettings) {

      $('.osm-map', context).each(function (i, mapElement) {
        const conf = drupalSettings.degov_media_address[mapElement.id];

        if (conf) {
          const elm = $(mapElement);
          // Kind of once: leaflet-container is set after init.
          if (!elm.hasClass('leaflet-container')) {

            // Create map and set center and zoom.
            const map = L.map(elm[0], {
              scrollWheelZoom: true,
              zoomControl: false,
              center: [conf.lat, conf.lon],
              zoom: 18
            });

            // Add basemap tiles and attribution.
            L.tileLayer('http://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png', {
              attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, &copy; <a href="http://cartodb.com/attributions">CartoDB</a>'
            }).addTo(map);

            const popup = Drupal.theme('degovMediaAddressPopup', conf.address);

            // Add pin & popup.
            const customPin = new L.Icon({iconUrl: conf.pin});
            L.marker([conf.lat, conf.lon], {icon: customPin}).bindPopup(popup).openPopup().addTo(map).openPopup();
          }
        }
      });
    }
  };

})(jQuery, Drupal, drupalSettings);
