import Navi from './../navi';
import SpeedChecker from './../speed_checker';

(function ($, Drupal) {
  'use strict';
  Drupal.behaviors.checkCellular = {
    attach: function (context, settings) {
      let videos = settings.degov_media_video_mobile;

      if (Drupal.behaviors.checkCellular.check()) {
        for (let id in videos) {
          let video = videos[id];
          if (typeof video['files']['video_mobile'] === 'string') {
            $('#' + video['id']).replaceWith(function () {
              return $(this).attr('src', video['files']['video_mobile']);
            });
          }
        }
      }
    },
    check: function () {
      let navi_connection = new Navi(navigator),
        connection = navi_connection.getConnection(),
        isCellular,
        speedChecker = new SpeedChecker(window);

      if (connection) {
        if (typeof connection.type !== 'undefined') {
          isCellular = (connection.type === 'cellular');
        } else if (typeof connection.effectiveType !== 'undefined') {
          isCellular = (connection.effectiveType !== '4g');
        }
      } else {
        isCellular = speedChecker.checkSlowLoadTime();
      }

      return isCellular;
    }
  }
})(jQuery, Drupal, drupalSettings);


