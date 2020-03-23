/**
 * @file
 * slideshow.js
 */

/**
 * Youtube player setup.
 */
function slider1_youtube() {
  jQuery('.slideshow.default .slideshow__slides .video-embed-field-provider-youtube iframe').each(function (index) {
    jQuery(this).on('load', function () {
      if (!jQuery(this).attr('id')) {
        var id = 'slider-youtube-video-' + index;
        jQuery(this).attr('id', id);
        new YT.Player(id, {
          events: {
            'onStateChange': onYouTubePlayerStateChange
          }
        });
      }
    });
  });
}

/**
 * Youtube API ready callback.
 */
function onYouTubeIframeAPIReady() {
  slider1_youtube();
}

/**
 * Youtube player state change callback.
 */
function onYouTubePlayerStateChange(event) {
  // Stop automatic progression when a video starts playing.
  if (event.data == YT.PlayerState.PLAYING) {
    if (jQuery('.slideshow.default .slick__pause').is(':visible')) {
      jQuery('.slideshow.default .slick__pause').trigger('click');
    }
  }
}

/**
 * Defines the behavior of the Slideshow paragraph.
 */
(function ($, Drupal, drupalSettings) {

  'use strict';

  /**
   * Initializes the slideshow paragraph with Slick.
   */
  Drupal.behaviors.slideshow = {
    attach: function (context, settings) {
      var $slideshow = $('.slideshow.default', context);
      if ($slideshow.length >= 1) {
        var $slider = $('.slideshow__slides', $slideshow);
        if ($slider.children().length > 1) {
          $slider.once().slick({
            dots: true,
            autoplay: false,
            speed: 500
          });

          $('.slick__pause', $slideshow).on('click', function () {
            $slider.slick('slickPause');
            $(this).hide().siblings('.slick__play').show().focus();
          }).hide();
          $('.slick__play', $slideshow).on('click', function () {
            $slider.slick('slickPlay');
            $(this).hide().siblings('.slick__pause').show().focus();
          }).show();

          // Vimeo player setup.
          var vimeo_players = [];
          function slider1_vimeo() {
            $slider.find('.video-embed-field-provider-vimeo iframe').each(function (index) {
              if (!$(this).attr('id')) {
                var id = 'slider-vimeo-video-' + index;
                $(this).attr('id', id);
                vimeo_players[id] = new Vimeo.Player($(this));
                vimeo_players[id].on('play', function () {
                  if ($slideshow.find('.slick__pause').is(':visible')) {
                    $slideshow.find('.slick__pause').trigger('click');
                  }
                });
              }
            });
          }

          // Youtube and Vimeo video handling.
          var observer_config = {childList: true, subtree: true};
          $slideshow.find('.video__video').each(function () {
            var target = this;
            var observer = new MutationObserver(function (mutations, observer) {
              slider1_vimeo();
              slider1_youtube();
            });
            observer.observe(target, observer_config);
          });

          // Stop automatic progression when a video starts playing.
          $slider.find('video').each(function () {
            $(this).on('play', function () {
              if ($slideshow.find('.slick__pause').is(':visible')) {
                $slideshow.find('.slick__pause').trigger('click');
              }
            });
          });

          // Pause videos when changing slides.
          $slider.on('beforeChange', function (event, slick, currentSlide, nextSlide) {
            pause_video(event, slick, currentSlide, nextSlide, vimeo_players);
          });

        }
        else {
          $slideshow.find('.paragraph-slideshow').addClass('single-slide');
        }
      }

      // Slick slider for prev/next thumbnails images.
      var $slideshow_prev_next = $('.slideshow-with-prev-next');
      var $slides_prev_next = $('.slides_prev_next', $slideshow_prev_next);
      $slides_prev_next.once().slick({
        dots: false,
        slidesToShow: 1,
        autoplay: false
      });
      setTimeout(function () {
        $slides_prev_next.prepend('<div class="prev-slick-img slick-thumb-nav"><img src="/prev.jpg" class="img-responsive"></div>').append('<div class="next-slick-img slick-thumb-nav"><img src="/next.jpg" class="img-responsive"></div>');
        get_prev_slick_img();
        get_next_slick_img();
      }, 500);

      $slides_prev_next.on('click', '.slick-prev', function () {
        get_prev_slick_img();
      });
      $slides_prev_next.on('click', '.slick-next', function () {
        get_next_slick_img();
      });
      $slides_prev_next.on('swipe', function (event, slick, direction) {
        if (direction == 'left') {
          get_prev_slick_img();
        }
        else {
          get_next_slick_img();
        }
      });

      function get_prev_slick_img() {
        var $slick_current = $('.slick-current');
        // For prev img.
        var prev_slick_img = $slick_current.prev('.slick-slide').find('.slide__media img').attr('src');
        $('.prev-slick-img img').attr('src', prev_slick_img);
        $('.prev-slick-img').css('background-image', 'url(' + prev_slick_img + ')');
        // For next img.
        var prev_next_slick_img = $slick_current.next('.slick-slide').find('.slide__media img').attr('src');
        $('.next-slick-img img').attr('src', prev_next_slick_img);
        $('.next-slick-img').css('background-image', 'url(' + prev_next_slick_img + ')');
      }

      function get_next_slick_img() {
        var $slick_current = $('.slick-current');
        // For next img.
        var next_slick_img = $slick_current.next('.slick-slide').find('.slide__media img').attr('src');
        $('.next-slick-img img').attr('src', next_slick_img);
        $('.next-slick-img').css('background-image', 'url(' + next_slick_img + ')');
        // For prev img.
        var next_prev_slick_img = $slick_current.prev('.slick-slide').find('.slide__media img').attr('src');
        $('.prev-slick-img img').attr('src', next_prev_slick_img);
        $('.prev-slick-img').css('background-image', 'url(' + next_prev_slick_img + ')');
      }

      if ($slides_prev_next.children().length > 1) {
        // Vimeo player setup.
        var vimeo_players = [];
        function slider2_vimeo() {
          $slides_prev_next.find('.video-embed-field-provider-vimeo iframe').each(function (index) {
            if (!$(this).attr('id')) {
              var id = 'slider-vimeo-video-' + index;
              $(this).attr('id', id);
              vimeo_players[id] = new Vimeo.Player($(this));
            }
          });
        }

        // Vimeo video handling.
        var observer_config = {childList: true, subtree: true};
        $slides_prev_next.find('.video__video').each(function () {
          var target = this;
          var observer = new MutationObserver(function (mutations, observer) {
            slider2_vimeo();
          });
          observer.observe(target, observer_config);
        });

        // Pause videos when changing slides.
        $slides_prev_next.on('beforeChange', function (event, slick, currentSlide, nextSlide) {
          pause_video(event, slick, currentSlide, nextSlide, vimeo_players);
        });
      }

      // Pause video on current slide.
      function pause_video(event, slick, currentSlide, nextSlide, vimeo_players) {
        var video = $(slick.$slides.get(currentSlide)).find('video');
        if (video.length) {
          video.get(0).pause();
        }
        video = $(slick.$slides.get(currentSlide)).find('.video-embed-field-provider-youtube iframe');
        if (video.length) {
          video.get(0).contentWindow.postMessage('{"event":"command","func":"pauseVideo","args":""}', '*');
        }
        video = $(slick.$slides.get(currentSlide)).find('.video-embed-field-provider-vimeo iframe');
        if (video.length && typeof vimeo_players[video.attr('id')] !== 'undefined') {
          vimeo_players[video.attr('id')].pause();
        }
      }

      // End.
    }
  }

})(jQuery, Drupal, drupalSettings);
