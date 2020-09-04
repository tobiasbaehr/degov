/**
 * @file
 * process.js
 *
 * Defines the behaviors of the degov_social_media_settings module.
 */

(function ($, Drupal, drupalSettings) {

  'use strict';

  Drupal.behaviors.degov_social_media_settings = {
    attach: function (context) {
      var $socialMediaDialog = $('#social-media-settings', context);
      // Move the modal outside of the page wrappers, to prevent styling overwrites.
      if ($('.social-media-settings.modal', context)) {
        $('.social-media-settings.modal').detach().appendTo('body');
      }

      // Initialize when cookies are accepted by eu_cookie_compliance module.
      $('.agree-button', context).once('social-media-settings').click(function () {
        initializeSettings();
        $('.js-social-media-wrapper').each(function () {
          applySettings($(this));
        })
      });

      // Open the modal.
      var $openSocialMediaSettings = $('.js-social-media-settings-open');
      $openSocialMediaSettings.click(function (e) {
        e.preventDefault();
        openModal();
      });

      // Save the settings.
      $('.js-social-media-settings-save', $socialMediaDialog).once('social-media-settings').click(function () {
        saveSettings();
      });

      // Apply the settings.
      $('.js-social-media-wrapper', context).once('social-media-settings').each(function () {
        applySettings($(this));
      });

      $('.js-social-media-source-all').once('social-media-settings').click(function () {
        handleAll($(this));
      });
      $socialMediaDialog.on('hidden.bs.modal', function () {
        $openSocialMediaSettings.focus();
        $socialMediaDialog.attr('aria-hidden', true);
      });
      $socialMediaDialog.on('shown.bs.modal', function () {
        $socialMediaDialog.attr('aria-hidden', false).find('h2').focus();
      });
    }
  };

  var modal = $('#social-media-settings');
  var cookie = 'degov_social_media_settings';
  var settings = drupalSettings.degov_social_media_settings;
  var code = settings.code;
  var sources = { };

  // Shows the social media settings link if cookie are allowed and
  // creates a cookie with default values.
  function initializeSettings() {
    if (typeof Drupal.eu_cookie_compliance !== 'undefined' && Drupal.eu_cookie_compliance.hasAgreed()) {
      $('.js-social-media-settings-open').removeClass('hidden');

      if (cookieExists()) {
        sources = cookieGetSettings();
      }
      else {
        sources = settings.sources;
        cookieSaveSettings();
      }
    }
  }

  // Applies the social media settings to a social media wrapper.
  function applySettings(wrapper) {
    var source = wrapper.attr('data-social-media-source');
    var entity = wrapper.attr('data-social-media-entity');
    var target = $('.js-social-media-code', wrapper);

    // Show the code if source is enabled.
    if (sources.hasOwnProperty(source) && sources[source] === true && code.hasOwnProperty(entity)) {
      target.html(code[entity]);

      if (source === 'twitter') {
        initTwitter(wrapper);
        initSoMeSlider('twitter');
      }

      if (source === 'instagram') {
        initInstagram();
        initSoMeSlider('instagram');
      }

      if (source === 'youtube') {
        initSoMeSlider('youtube');
      }
    }
    else {
      if (typeof Drupal.eu_cookie_compliance !== 'undefined' && Drupal.eu_cookie_compliance.hasAgreed()) {
        var link = $('<div class="js-social-media-code__message">' + settings.link + '</div>');

        $('.js-social-media-settings-open', link).click(function (e) {
          e.preventDefault();
          openModal();
        });

        target.html(link);
        target.parent().find(".slick-controls").hide();
      }
      else {
        target.html('<div class="js-social-media-code__message">' + settings.cookie + '</div>');
      }
    }
  }

  // Opens the social media settings modal.
  function openModal() {
    // Update checkboxes with settings from cookie.
    $('.js-social-media-source', modal).each(function () {
      var source = $(this).val();

      if (sources.hasOwnProperty(source)) {
        $(this).prop('checked', sources[source]);
      }
    });

  }

  // Handle click on 'all' checkbox.
  function handleAll(all) {
    $('.js-social-media-source').prop('checked', all.is(':checked'));
  }

  // Saves the social media settings in the cookie and applies the new
  // settings to all social media wrappers.
  function saveSettings() {
    // Update the sources variable.
    $('.js-social-media-source', modal).each(function () {
      var source = $(this).val();

      if (sources.hasOwnProperty(source)) {
        sources[source] = $(this).is(':checked');
      }
    });

    // Save settings in cookie.
    if (Drupal.eu_cookie_compliance.hasAgreed()) {
      cookieSaveSettings();
    }

    // Apply new settings.
    $('.js-social-media-wrapper').each(function () {
      applySettings($(this));
    });
  }

  // Initialize twitter media from media bundle tweet.
  function initTwitter(wrapper) {
    function _initTwitter() {
      twttr.widgets.load(wrapper);
    }

    if (typeof twttr === 'undefined') {
      $.ajax({
        url: '//platform.twitter.com/widgets.js',
        type: "GET",
        success: _initTwitter,
        dataType: "script",
        cache: true
      });
    }
    else {
      _initTwitter();
    }
  }

  // Initialize instagram media from media bundle instagram.
  function initInstagram() {
    function _initInstagram() {
      instgrm.Embeds.process();
    }

    if (typeof instgrm === 'undefined') {
      $.ajax({
        url: '//platform.instagram.com/en_US/embeds.js',
        type: "GET",
        success: _initInstagram,
        dataType: "script",
        cache: true
      });
    }
    else {
      _initInstagram();
    }
  }

  // Checks if the cookie exists.
  function cookieExists() {
    return typeof $.cookie(cookie) !== 'undefined';
  }

  // Reads, parses and returns the settings from the cookie.
  function cookieGetSettings() {
    return JSON.parse($.cookie(cookie));
  }

  // Saves the settings in the cookie.
  function cookieSaveSettings() {
    $.cookie(cookie, JSON.stringify(sources), { path: '/' });
  }

  // Initialize.
  initializeSettings();

  function initSoMeSlider(source) {
    var slider = (source === 'twitter') ? $('.tweets-slideshow .tweets') : $('.' + source + '-preview'),
        sliderWrap = slider.parent(),
        defaultSettings = {
          dots: true,
          infinite: true,
          speed: 300,
          slidesToShow: 1,
          slidesToScroll: 1,
          autoplay: true,
          arrows: true,
          appendArrows: $('.l-slick-navi', slider.parent()),
          appendDots: $('.l-slick-navi', slider.parent()),
          mobileFirst: true,
          adaptiveHeight: true,

          responsive: [
            {
              breakpoint: 720,
              settings: {
                slidesToShow: 2,
                slidesToScroll: 2
              }
            }
          ]
        };

    // Responsive override.
    if (source === 'youtube') {
      defaultSettings = mergeOptions(defaultSettings, {
        responsive: [
          {
            breakpoint: 420,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 2
            }
          },
          {
            breakpoint: 840,
            settings: {
              slidesToShow: 3,
              slidesToScroll: 3
            }
          }
        ]
      })
    }

    if (slider.length) {
      var slickControlls = sliderWrap.find('.slick-controls');

      slickControlls.show();
      slider.slick(defaultSettings);
      slickControlls.find('.slick__pause').click(function () {
        slider.slick('slickPause');
        $(this).hide().siblings('.slick__play').show();
      });
      var slickPlay = slickControlls.find('.slick__play');
      slickPlay.hide(); // Initial hide.
      slickPlay.on('click', function () {
        slider.slick('slickNext').slick('slickPlay');
        $(this).hide().siblings('.slick__pause').show();
      });
    }
  }

  // Parameter to open the social media settings overlay on page load for testing purposes.
  if (window.location.search === '?_debugDisplaySocialMediaSettings') {
    $('.js-social-media-settings-open').trigger('click');
  }

  /**
   * Overwrites obj1's values with obj2's and adds obj2's if non existent in obj1
   * @param obj1
   * @param obj2
   * @returns obj3 a new object based on obj1 and obj2
   */
    function mergeOptions(obj1,obj2){
    var obj3 = {};
    for (var attrname in obj1) { obj3[attrname] = obj1[attrname]; }
    for (var attrname in obj2) { obj3[attrname] = obj2[attrname]; }
    return obj3;
  }

})(jQuery, Drupal, drupalSettings);
