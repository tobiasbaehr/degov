<?php

namespace Drupal\youtube_feed_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\facets\Exception\Exception;
use Madcoda\Youtube\Youtube;

/**
 * Provides a 'YouttubeFeedBlock' block.
 *
 * @Block(
 *  id = "youtube_feed_block",
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
      ->get('youtube_feed_block.settings');

    $apiKey = $config->get('api_key');
    $channelName = $config->get('channel');
    $numberOfVideos = $config->get('number_of_videos');


    try {
      $youtube = new Youtube(['key' => $apiKey]);
      $channelID = $youtube->getChannelByName($channelName)->id;
      if(!$channelID) $channelID = $channelName;
      $videos = $youtube->searchChannelVideos('', $channelID, $numberOfVideos, 'date');
      $test="";


      foreach ($videos as $video) {
        $info = $youtube->getVideoInfo($video->id->videoId);
        $build['youtube_feed_block'][] = [
          '#theme' => 'youtube_feed_block',
          '#title' => $video->snippet->title,
          '#likes' => $info->statistics->likeCount,
          '#views' => $info->statistics->viewCount,
          '#comments' => $info->statistics->commentCount,
          '#videoID' => $video->id->videoId,
          '#thumbnail' => $video->snippet->thumbnails->default->url,
          '#cache' => ['max-age' => (60 * 60)],
          '#link_display' => $this->_shortDescription("https://youtube.com/watch?v=" . $video->id->videoId, 32, '...'),
        ];
      }
    }
    catch(Exception $e) {
      $build['#markup'] = '<span class="error">'.$e."</span>";

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

