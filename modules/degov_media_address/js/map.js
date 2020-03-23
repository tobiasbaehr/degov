/**
 * @file
 * map.js
 *
 * Open Street Map for Address paragraph.
 */

(function ($, Drupal, drupalSettings) {
  'use strict';

  /**
   * Initializes the Map with Leaflet.
   */
  Drupal.behaviors.address = {
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

  /**
   * @see degov_media_address_get_js_fileds()
   * @param data
   * @return {string}
   */
  Drupal.theme.degovMediaAddressPopup = function (data) {
    if (data) {
      let html = data.title + '<br />' +
        data.organization + '<br />' +
        data.address_line1 + '<br />';
      if (data.address_line2) {
        html += data.address_line2 + '<br />';
      }
      html += data.postal_code + ' ' + data.locality + '<br />';
      if (data.phone_number) {
        html += Drupal.t('Telephone') + ': ' + data.phone_number + '<br />';
      }
      if (data.fax_number) {
        html += Drupal.t('Fax') + ': ' + data.fax_number + '<br />';
      }
      if (data.email) {
        html += Drupal.t('Email') + ': <a href="mailto:' + data.email + '">' + data.email + '</a><br />';
      }
      if (data.link_title && data.link_uri) {
        html += Drupal.t('Link') + ': <a href="' + data.link_uri + '" title="' + data.link_title + '" target="_blank">' + data.link_title + '</a>';
      }
      if (!data.link_title && data.link_uri) {
        html += Drupal.t('Link') + ': <a href="' + data.link_uri + '" title="' + data.link_uri + '" target="_blank">' + data.link_uri + '</a>';
      }
      return html;
    }
    return '';
  };

})(jQuery, Drupal, drupalSettings);
