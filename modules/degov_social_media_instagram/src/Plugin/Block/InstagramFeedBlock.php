<?php

namespace Drupal\instagram_feed_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use InstagramScraper\Instagram;

/**
 * Provides a 'YouttubeFeedBlock' block.
 *
 * @Block(
 *  id = "instagram_feed_block",
 *  admin_label = @Translation("Instagram Feed Block"),
 * )
 */
class InstagramFeedBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    $config = \Drupal::service('config.factory')
      ->get('instagram_feed_block.settings');
    $user = $config->get('user');
    $max = $config->get('number_of_posts');
    $maxLenth = $config->get('number_of_characters');

    if (is_numeric($max)) {
      $max = intval($max);
    }

    $instagram = new Instagram();
    $nonPrivateAccountMedias = $instagram->getMedias($user, $max);

    foreach ($nonPrivateAccountMedias as $media) {
      $build['instagram_feed_block'][] = [
        '#theme' => 'instagram_feed_block',
        '#imageUrl' => $media->getImageThumbnailUrl(),
        '#link' => $media->getLink(),
        '#link_display' => $this->_shortDescription($media->getLink(),32,'...'),
        '#type' => $media->getType(),
        '#caption' => $this->_shortDescription($media->getCaption(), $maxLenth, "..."),
        '#views' => $media->getVideoViews(),
        '#likes' => $media->getLikesCount(),
        '#comments' => $media->getCommentsCount(),
        '#date' => \Drupal::service('date.formatter')
        ->formatTimeDiffSince($media->getCreatedTime()),
        '#cache' => ['max-age' => (60 * 5)],
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
