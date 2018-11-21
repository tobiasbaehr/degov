/**
 * Defines the behavior of the media bundle video.
 */
(function ($, Drupal, drupalSettings) {

  'use strict';

  /**
   * Behavior for Video Transcript acordion
   */
  Drupal.behaviors.videoMobile = {
    attach: function (context, settings) {
      $('.video-mobile__transcription').once('video-mobile-js').each(function(){
        $('.video-mobile__transcription__header').click(function(){
          $('.video-mobile__transcription__body').slideToggle();
          $('i', this).toggleClass('fa-caret-right fa-caret-down');
        });
      });
    }
  }

})(jQuery, Drupal, drupalSettings);
