(function ($, Drupal, drupalSettings) {
    'use strict';
    Drupal.behaviors.checkCellular = {
        attach: function (context, settings) {
          let videos = settings.degov_media_video_mobile.checkCellular;
          if (videos['video_mobile'] && Drupal.behaviors.checkCellular.check()){
            jQuery('#'+videos['id']).replaceWith(function(){
              return $(this).attr('src',videos['video_mobile']);
            });
          }
        },
        check: function () {
          const maxCellularLoadTime = 2000;
          let loadTime,isCellular,connection;
          if (typeof navigator.connection !== 'undefined') {
            connection = navigator.connection;
          }else if(typeof navigator.mozConnection !== 'undefined'){
            connection = navigator.mozConnection;
          }else if(typeof navigator.webkitConnection !== 'undefined'){
            connection = navigator.webkitConnection;
          }
          if (connection) {
            if(typeof connection.type !== 'undefined'){
              isCellular = (connection.type === 'cellular');
            }else if(typeof connection.effectiveType !== 'undefined'){
              isCellular = (connection.effectiveType !== '4g');
            }
          }else {
            loadTime = new Date().valueOf() - window.performance.timing.requestStart;
            isCellular = (loadTime > maxCellularLoadTime);
          }
          return isCellular;
        }
    }
})(jQuery, Drupal, drupalSettings);


