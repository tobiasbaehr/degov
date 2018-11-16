
(function ($, Drupal, drupalSettings) {

    'use strict';
    Drupal.behaviors.checkCellular = {
      attach: function (context, settings) {
        let videos = settings.degov_media_video_mobile.checkCellular;
        console.log(videos);

        if (videos['video_mobile'] && Drupal.behaviors.checkCellular.check()) {
          jQuery('#' + videos['id']).replaceWith(function () {
            return $(this).attr('src', videos['video_mobile']);
          });
        }
      },
      check: function () {
        let navi_connection = new navi();
        let connection = navi_connection.getConnection(), isCellular, speedChecker = new speed_checker();
        //let connection = this.getConnection(), isCellular;

        if (connection) {
          if (typeof connection.type !== 'undefined') {
            isCellular = (connection.type === 'cellular');
          } else if (typeof connection.effectiveType !== 'undefined') {
              isCellular = (connection.effectiveType !== '4g');
          }
        } else {
          //isCellular = this.checkSlowLoadTime();
          isCellular = speedChecker.checkSlowLoadTime();
        }

      },
      getConnection: function () {
        let connection;
        console.log(navigator);
        if (typeof navigator.connection !== 'undefined') {
          connection = navigator.connection;
        } else if (typeof navigator.mozConnection !== 'undefined') {
          connection = navigator.mozConnection;
        } else if (typeof navigator.webkitConnection !== 'undefined') {
          connection = navigator.webkitConnection;
        }
        return connection;
      },
      checkSlowLoadTime: function () {
        const maxCellularLoadTime = 2000;
        let loadTime = new Date().valueOf() - window.performance.timing.requestStart;
        return (loadTime > maxCellularLoadTime);
      },
    }
})(jQuery, Drupal, drupalSettings);


