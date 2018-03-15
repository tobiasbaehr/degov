<?php

namespace Drupal\youtube_feed_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
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

    $youtube = new Youtube(['key' => $apiKey]);
    $channelID = $youtube->getChannelByName($channelName)->id;
    $videos = $youtube->searchChannelVideos('', $channelID, $numberOfVideos, 'date');

    foreach ($videos as $video) {
      $build['youtube_feed_block'][] = [
        '#theme' => 'youtube_feed_block',
        '#title' => $video->snippet->title,
        '#videoID' => $video->id->videoId,
        '#thumbnail' =>$video->snippet->thumbnails->default->url,
        '#cache' => ['max-age' => (60*60)]
      ];
    }

    return $build;
  }

}
