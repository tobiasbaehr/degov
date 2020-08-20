<?php

namespace Drupal\degov_social_media_youtube;

use Drupal\Core\Config\ConfigFactoryInterface;
use Madcoda\Youtube\Youtube as MadcodaYoutube;

/**
 * Class Youtube.
 *
 * @package Drupal\degov_social_media_youtube
 */
class Youtube extends MadcodaYoutube implements YoutubeInterface {

  /**
   * Youtube constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config
   *   The configuration factory.
   */
  public function __construct(ConfigFactoryInterface $config) {

    $params = [
      'key' => $config->get('degov_social_media_youtube.settings')->get('api_key')
    ];
    try {
      parent::__construct($params);
    }
    catch (\Exception $e) {
    }
  }

}
