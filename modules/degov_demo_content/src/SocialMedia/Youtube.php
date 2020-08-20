<?php

namespace Drupal\degov_demo_content\SocialMedia;

use Drupal\degov_social_media_youtube\YoutubeInterface;
use Madcoda\Youtube\Youtube as MadcodaYoutube;

/**
 * Class Youtube.
 *
 * @package Drupal\degov_demo_content\SocialMedia
 */
class Youtube extends MadcodaYoutube implements YoutubeInterface {

  use SocialMediaAssetsTrait;

  /**
   * {@inheritDoc}
   */
  public function __construct($params = [], $sslPath = NULL) {
    return;
  }

  /**
   * {@inheritDoc}
   */
  public function getChannelByName($username, $optionalParams = FALSE) {
    return FALSE;
  }

  /**
   * {@inheritDoc}
   */
  public function searchChannelVideos($q, $channelId, $maxResults = 10, $order = NULL) {
    return self::getDataFromFile('youtube', 'videos.txt');
  }

  /**
   * {@inheritDoc}
   */
  public function getVideoInfo($vId) {
    return self::getDataFromFile('youtube', "video_info_{$vId}.txt");
  }

}
