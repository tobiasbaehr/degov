<?php

namespace Drupal\degov_common\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;

/**
 * Class UserProfileLink.
 *
 * @Block(
 *   id = "degov_user_profile_link",
 *   admin_label = @Translation("Login / Profile link"),
 *   category = @Translation("Blocks")
 * )
 */
class UserProfileLink extends BlockBase implements BlockPluginInterface {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $block = [
      '#theme'     => 'degov_user_profile_link',
      '#logged_in' => \Drupal::currentUser()->isAuthenticated(),
      '#cache'     => [
        'user',
      ],
    ];

    return $block;
  }
}
