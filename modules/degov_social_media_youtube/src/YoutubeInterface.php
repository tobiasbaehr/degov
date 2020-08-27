<?php

declare(strict_types=1);

namespace Drupal\degov_social_media_youtube;

/**
 * Interface YoutubeInterface
 *
 * @package Drupal\degov_social_media_youtube
 */
interface YoutubeInterface {

  public function getChannelByName($username, $optionalParams = FALSE);

  public function searchChannelVideos($q, $channelId, $maxResults = 10, $order = NULL);

  public function getVideoInfo($vId);

}
