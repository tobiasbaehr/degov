/**
 * @file
 * map.js
 *
 * Defines the behavior of the Address paragraph.
 */

(function ($, Drupal, drupalSettings) {

  'use strict';

  /**
   * Initializes the Map with Leaflet.
   */
  Drupal.behaviors.address = {
    attach: function (context, settings) {
      if (typeof settings.maps === "undefined" || settings.maps.length === 0) {
        return;
      }

      var maps = [];

      function isEmpty(str) {
        return (!str || 0 === str.length);
      }

      // Loop through all available maps.
      $.each(settings.maps, function (index, value) {
        var selector = '#' + index;
        if (typeof value.type === "undefined") {
          return;
        }
        if (!$(selector).hasClass('leaflet-container')) {
          // Create map and set center and zoom.
          maps[index] = L.map(index, {
            scrollWheelZoom: true,
            zoomControl: false,
            center: [value.lat, value.lon],
            zoom: 18
          });

          // Add basemap tiles and attribution.
          L.tileLayer('http://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, &copy; <a href="http://cartodb.com/attributions">CartoDB</a>'
          }).addTo(maps[index]);

          let tooltip = drupalSettings.media_entity_fields.title + '<br />' +
            drupalSettings.media_entity_fields.organization + '<br />' +
            drupalSettings.media_entity_fields.address_line1 + '<br />';

          if (!isEmpty(drupalSettings.media_entity_fields.address_line2)) {
            tooltip += drupalSettings.media_entity_fields.address_line2 + '<br />';
          }

          tooltip += drupalSettings.media_entity_fields.postal_code + ' ' + drupalSettings.media_entity_fields.locality + '<br />';

          if (!isEmpty(drupalSettings.media_entity_fields.phone_number)) {
            tooltip += Drupal.t('Telephone') + ': ' + drupalSettings.media_entity_fields.phone_number + '<br />';
          }

          if (!isEmpty(drupalSettings.media_entity_fields.fax_number)) {
            tooltip += Drupal.t('Fax') + ': ' + drupalSettings.media_entity_fields.fax_number + '<br />';
          }

          if (!isEmpty(drupalSettings.media_entity_fields.email)) {
            tooltip += Drupal.t('Email') + ': <a href="mailto:' + drupalSettings.media_entity_fields.email + '">' + drupalSettings.media_entity_fields.email + '</a><br />';
          }

          if (!isEmpty(drupalSettings.media_entity_fields.link_title) && !isEmpty(drupalSettings.media_entity_fields.link_uri)) {
            tooltip += Drupal.t('Link') + ': <a href="' + drupalSettings.media_entity_fields.link_uri + '" title="' + drupalSettings.media_entity_fields.link_title + '" target="_blank">' + drupalSettings.media_entity_fields.link_title + '</a>';
          }

          if (isEmpty(drupalSettings.media_entity_fields.link_title) && !isEmpty(drupalSettings.media_entity_fields.link_uri)) {
            tooltip += Drupal.t('Link') + ': <a href="' + drupalSettings.media_entity_fields.link_uri + '" title="' + drupalSettings.media_entity_fields.link_uri + '" target="_blank">' + drupalSettings.media_entity_fields.link_uri + '</a>';
          }

          // Add pin.
          var customPin = new L.Icon({iconUrl: value.pin});
          L.marker([value.lat, value.lon], {icon: customPin}).bindPopup(tooltip).openPopup().addTo(maps[index]);
        }
      });
    }
  };

})(jQuery, Drupal, drupalSettings);
