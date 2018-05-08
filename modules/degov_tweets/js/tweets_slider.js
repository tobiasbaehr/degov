/**
 * @file slideshow.js
 *
 * Defines the behavior of the Slideshow paragraph.
 */
(function ($, Drupal) {

  'use strict';

  // Slick slider in Twitter block
  Drupal.behaviors.slickTweets = {
    attach: function (context, settings) {

      $('.tweets-slideshow').show();

      if ($.cookie("degov_social_media_settings") && $.parseJSON($.cookie("degov_social_media_settings")).twitter) {
        $('.tweets-slideshow .tweets').once().slick({
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
        var dataSafetyInfo = '<div class="js-social-media-code__message">' + Drupal.t('This social media source is disabled. You can enable it in the <a href="#" class="js-social-media-settings-open">social media settings</a>.') + '</div>';
        $('.tweets-slideshow .tweets').html(dataSafetyInfo);
      }

    }
  };

})(jQuery, window.Drupal);
