<?php

namespace Drupal\youtube_feed_block\Factory;

use Madcoda\Youtube\Youtube;

class YouTubeFactory {

  public function create(string $apiKey) {
    try {
      return new Youtube(['key' => $apiKey]);
    } catch (\InvalidArgumentException $e) {
      \Drupal::logger('youtube_feed_block')->error($e->getMessage());
    } catch (\Exception $e) {
      \Drupal::logger('youtube_feed_block')->error($e->getMessage());
    }
  }

}