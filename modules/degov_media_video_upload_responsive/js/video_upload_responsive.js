/**
 * Defines the behavior of the media bundle video.
 */
(function ($, Drupal, drupalSettings) {

  'use strict';

  /**
   * Behavior for Video Transcript acordion
   */
  Drupal.behaviors.videoUploadResponsive = {
    attach: function (context, settings) {
      $('.video-upload-responsive__transcription').once('video-upload-responsive-js').each(function(){
        $('.video-upload-responsive__transcription__header').click(function(){
          $('.video-upload-responsive__transcription__body').slideToggle();
          $('i', this).toggleClass('fa-caret-right fa-caret-down');
        });
      });
    }
  }

})(jQuery, Drupal, drupalSettings);
