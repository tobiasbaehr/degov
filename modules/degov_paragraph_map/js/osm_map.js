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

        let map = L.map($('.osm-map#' + mapElement.id).not('.leaflet-container')[0], {
          scrollWheelZoom: true,
          zoomControl: false,
          center: [0, 0],
          zoom: 18
        });

        const mapAddresses = drupalSettings.degov_media_address[mapElement.id];

        if (mapAddresses) {

          let markers = [];

          mapAddresses.forEach(function(addressElement) {

            const popup = Drupal.theme('degovMediaAddressPopup', addressElement.address),
              customPin = new L.Icon({iconUrl: addressElement.pin});

            L.tileLayer('http://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png', {
              attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, &copy; <a href="https://cartodb.com/attributions">CartoDB</a>'
            }).addTo(map);

            let marker = L.marker([addressElement.lat, addressElement.lon], {icon: customPin});

            // Use the following line, if you want to open the address popup initially.
            // marker.bindPopup(popup, {autoClose:false}).addTo(map).openPopup();
            marker.bindPopup(popup, {autoClose:false}).addTo(map);

            map.panTo(new L.LatLng(addressElement.lat, addressElement.lon));

            markers.push(marker);
          });

          let group = new L.featureGroup(markers);
          map.fitBounds(group.getBounds());

        }
      });
    }
  };

})(jQuery, Drupal, drupalSettings);
