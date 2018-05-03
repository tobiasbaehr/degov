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
      if ($.parseJSON($.cookie("degov_social_media_settings")).youtube) {
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
      } else {
        var dataSafetyInfo = Drupal.t('This social media source is disabled. You can enable it in the <a href="#" class="js-social-media-settings-open">social media settings</a>.');
        $('.youtube-preview').html(dataSafetyInfo);
      }

    }
  };

})(jQuery, window.Drupal);
