<?php

namespace Drupal\degov_search_media_solr\Plugin\Block;

use Drupal\block\Entity\Block;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a block to filter the media search.
 *
 * @Block(
 *   id = "degov_search_media_solr_filter",
 *   admin_label = @Translation("deGov search media Solr filters")
 * )
 */
class DegovSearchMediaFilter extends BlockBase {

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowed();
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $form['filter'] = [
      '#type' => 'details',
      '#title' => t('Filter media search results'),
      '#open' => TRUE,
      '#attributes' => ['class' => ['block-degov-search-media-filter']],
    ];

    $ids = [
      'degov_theme_search_media_bundle',
      'degov_theme_search_media_tags',
      'search_content_topic',
      'degov_theme_media_publish_date',
    ];
    foreach ($ids as $id) {
      $block = Block::load($id);
      if ($block) {
        $block->disable();
        $form['filter'][$id] = \Drupal::entityTypeManager()
          ->getViewBuilder('block')
          ->view($block);
      }
    }

    return $form;
  }

}
