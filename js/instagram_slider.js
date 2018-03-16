/**
 * @file instagram_slider.js
 *
 * Defines the behavior of the Instagram slider.
 */
(function ($, Drupal, drupalSettings) {

  'use strict';

  // Slick slider in Twitter block
  Drupal.behaviors.slickInstagram = {
    attach: function (context, settings) {
      $('.instagram-preview').once().slick({
        dots: true,
        infinite: false,
        speed: 300,
        slidesToShow: 2,
        slidesToScroll: 2,
        autoplay: true,
        responsive: [
          {
            breakpoint: 720,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1
            }
          }
        ]
      });

      $('.slick__pause').on('click', function () {
        $('.instagram-preview').once().slick('slickPause');
        $(this).hide().siblings('.slick__play').show();
      });
      $('.slick__play').on('click', function () {
        $('.instagram-preview').once().slick('slickPlay');
        $(this).hide().siblings('.slick__pause').show();
      });
    }
  };

})(jQuery, window.Drupal);
