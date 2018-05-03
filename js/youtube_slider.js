/**
 * @file instagram_slider.js
 *
 * Defines the behavior of the Instagram slider.
 */
(function ($, Drupal, drupalSettings) {

  'use strict';

  // Slick slider in Youtube block
  Drupal.behaviors.slickYoutube = {
    attach: function (context, settings) {
      $.get( "/youtube-feed-ajax", function( data ) {
        $( ".youtube-feed-block" ).html( data ).promise().done(function(){
          $('.youtube-preview').once().slick({
            dots: true,
            infinite: false,
            speed: 300,
            slidesToShow: 2,
            slidesToScroll: 2,
            autoplay: true,
            responsive: [
              {
                breakpoint: 1319,
                settings: {
                  slidesToShow: 1,
                  slidesToScroll: 1
                }
              }
            ]
          });

          $('.slick__pause').on('click', function () {
            $('.youtube-preview').once().slick('slickPause');
            $(this).hide().siblings('.slick__play').show();
          });
          $('.slick__play').on('click', function () {
            $('.youtube-preview').once().slick('slickPlay');
            $(this).hide().siblings('.slick__pause').show();
          });
        });

        console.log('foo');

      });
    }
  };

})(jQuery, window.Drupal);
