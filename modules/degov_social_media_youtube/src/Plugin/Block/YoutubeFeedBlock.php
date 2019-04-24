<?php

namespace Drupal\degov_social_media_youtube\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Madcoda\Youtube\Youtube;

/**
 * Provides a 'YouttubeFeedBlock' block.
 *
 * @Block(
 *  id = "degov_social_media_youtube",
 *  admin_label = @Translation("Youtube Feed Block"),
 * )
 */
class YoutubeFeedBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    $config = \Drupal::service('config.factory')
      ->get('degov_social_media_youtube.settings');

    $apiKey = $config->get('api_key');
    $channelName = $config->get('channel');
    $numberOfVideos = $config->get('number_of_videos');

    try {
      $youtube = new Youtube(['key' => $apiKey]);
    } catch(\InvalidArgumentException $exception) {
      \Drupal::logger('degov_social_media_youtube')->warning('No valid YouTube api key. Therefor no twig template variables created.');
      return $build;
    }

    $channelID = NULL;
    if (!empty($youtube->getChannelByName($channelName))) {
      $channelID = $youtube->getChannelByName($channelName)->id;
    }
    else {
      // If no channel was found by that name then take the channel name as ID
      $channelID = $channelName;
    }
    $videos = $youtube->searchChannelVideos('', $channelID, $numberOfVideos, 'date');

    foreach ($videos as $video) {
      $info = $youtube->getVideoInfo($video->id->videoId);
      $build['degov_social_media_youtube'][] = [
        '#theme' => 'degov_social_media_youtube',
        '#title' => $video->snippet->title,
        '#description' => $this->_shortDescription($video->snippet->description, 123, '...'),
        '#likes' => $info->statistics->likeCount,
        '#dislikes' => $info->statistics->dislikeCount,
        '#views' => $info->statistics->viewCount,
        '#comments' => (property_exists($info->statistics, 'commentCount')) ? $info->statistics->commentCount : NULL,
        '#videoID' => $video->id->videoId,
        '#thumbnail' => $video->snippet->thumbnails->default->url,
        '#cache' => ['max-age' => (60 * 60)],
        '#link_display' => $this->_shortDescription("https://youtube.com/watch?q=" . $video->id->videoId, 32, '...'),
      ];
    }
    return $build;
  }

  function _shortDescription($string, $maxLenth, $replacement) {
    if (strlen($string) > $maxLenth) {
      return substr($string, 0, $maxLenth) . $replacement;
    }
    else {
      return $string;
    }
  }
}

