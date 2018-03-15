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

    $instagram = new Instagram();
    $nonPrivateAccountMedias = $instagram->getMedias($user, $max);

    foreach ($nonPrivateAccountMedias as $media) {
      $build['instagram_feed_block'][] = [
        '#theme' => 'instagram_feed_block',
        '#imageUrl' => $media->getImageLowResolutionUrl(),
        '#link' => $media->getLink(),
        '#type' => $media->getType(),
        '#caption' => $media->getCaption(),
        '#cache' => ['max-age' => (60 * 5)],
      ];
    }

    return $build;
  }

}
