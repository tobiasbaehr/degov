/**
 * @file
 * gallery.js
 *
 * Defines the behavior of the media bundle gallery.
 */

(function ($, Drupal) {

  'use strict';

  /**
   * Initializes the slideshow with Slick and PhotoSwipe.
   */
  Drupal.behaviors.gallery = {
    count: 0,
    attach: function (context, settings) {
      $('.pswp__media-gallery', context).each(function (index) {
          initPswpMediagallery(this, $(this).next(), settings);
      });
    }
  };

  function copyImageCopyrightGallery($slider, context){
    $slider.on('init', function () {
      $('.media-gallery__image-info:not(.small_gallery)', context).html($('.slick-current .image__info', context).html());
      $('.media-gallery__image-info .small_gallery', context).html($('.slick-current .image__info', context).find('span').html());

      if ($('.slick-current .image__info', context).find('span').html() === '/') {
        $('.media-gallery__image-info.small_gallery', context).html($('.slick-current .image__info', context).html());
        $('.media-gallery__image-info.small_gallery span:first', context).remove();
      }
else {
        $('.media-gallery__image-info.small_gallery', context).html($('.slick-current .image__info', context).html());
      }
    });

    $slider.on('afterChange', function () {
      $('.media-gallery__image-info:not(.small_gallery)', context).html($('.slick-current .image__info', context).html());

      if ($('.slick-current .image__info', context).find('span').html() === '/') {
        $('.media-gallery__image-info.small_gallery', context).html($('.slick-current .image__info', context).html());
        $('.media-gallery__image-info.small_gallery span:first', context).remove();
      }
else {
        $('.media-gallery__image-info.small_gallery', context).html($('.slick-current .image__info', context).html());
      }
    });
  }

  function sliderInitUpdate($slider, settings, context, gallery) {
    $slider.on('init reInit afterChange', {value: Drupal.behaviors.gallery.count}, function (event, slick, currentSlide) {
      let i = (currentSlide ? currentSlide : 0) + 1;
      if (settings.degov_media_gallery.imagesDownloadLinks[event.data.value][$slider.slick('slickCurrentSlide')].field_allow_download === "0") {
        $('.slick-controls__gallery .slick__download', context).hide();
        $('.slick-controls__gallery .slick__lightroom', context).css("right", "0px");
      }
      else if (settings.degov_media_gallery.imagesDownloadLinks[event.data.value][$slider.slick('slickCurrentSlide')].field_allow_download === "1") {
        $('.slick-controls__gallery .slick__download', context).show();
        $('.slick-controls__gallery .slick__lightroom', context).css("right", "129px");
      }

      $('.slick__counter__current', gallery).text(i);
      $('.slick__counter__total', gallery).text(slick.slideCount);
      $('.slick-controls__gallery .slick__download a', gallery).prop('href', settings.degov_media_gallery.imagesDownloadLinks[event.data.value][$slider.slick('slickCurrentSlide')].uri);
    });
  };

  function getImageAndImageDataForSlideshow($images, settings){
    let $imageIndex = 0;
    let currentImages = [];
    $.each($images, function () {
      let $pswpItem;
      try {
        $pswpItem = {
          src: settings.degov_media_gallery.imagesDownloadLinks[Drupal.behaviors.gallery.count][$imageIndex].uri,
          w: settings.degov_media_gallery.imagesDownloadLinks[Drupal.behaviors.gallery.count][$imageIndex].width,
          h: settings.degov_media_gallery.imagesDownloadLinks[Drupal.behaviors.gallery.count][$imageIndex].height
        };
      }
      catch (e) {
        console.log(e.message);
        $pswpItem = [];
      }
      currentImages.push($pswpItem);
      $imageIndex++;
    });
    return currentImages;
  };

  function initPswpMediagallery(pswdMediagallery, context, settings){
    let gallery = $('.media-gallery__images', context);
    let $slider = $('.slideshow__slides', gallery);
    let $images = $slider.find('img');

    copyImageCopyrightGallery($slider, context);

    $slider.once().slick({
      dots: false,
      autoplay: false,
      arrows: true,
      swipeToSlide: true
    });

    let currentImages = getImageAndImageDataForSlideshow($images, settings);

    $('.slick-controls__gallery', gallery).once().append(
      '<span class="slick__download"><a href="' +
      settings.degov_media_gallery.imagesDownloadLinks[Drupal.behaviors.gallery.count][0].uri +
      '"><i aria-hidden="true" class="fa fa-download"></i>' +
      Drupal.t('Download') +
      '</a></span>'
    );

    if (settings.degov_media_gallery.imagesDownloadLinks[0].field_allow_download === "0") {
      $('.slick-controls__gallery .slick__download').hide();
      $('.slick-controls__gallery .slick__lightroom').css("right", "0px");
    }
else if (settings.degov_media_gallery.imagesDownloadLinks[0].field_allow_download === "1") {
      $('.slick-controls__gallery .slick__download').show();
      $('.slick-controls__gallery .slick__lightroom').css("right", "129px");
    }

    $('.media-gallery-js-open-lightroom', gallery).on("click", {value : currentImages}, function (event) {
      let $index = parseInt($slider.slick('slickCurrentSlide'));
      let $options = {
        index: $index
      };

      let $pswp = new PhotoSwipe(pswdMediagallery, PhotoSwipeUI_Default, event.data.value, $options);
      $pswp.init();
    });

    sliderInitUpdate($slider, settings, context, gallery);

    $('.slick__pause', gallery).on('click', function () {
      $slider.slick('slickPause');
      $(this).hide().siblings('.slick__play').show().focus();
    }).hide();

    $('.slick__play', gallery).on('click', function () {
      $slider.slick('slickPlay');
      $(this).hide().siblings('.slick__pause').show().focus();
    }).show();

    Drupal.behaviors.gallery.count++;
  }

})(jQuery, Drupal);
