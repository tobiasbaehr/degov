<?php

namespace Drupal\degov_search_content_solr\Plugin\Block;

use Drupal\block\Entity\Block;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Template\TwigEnvironment;


/**
 * Combines facets by blocks into a single block for filtering the search results.
 *
 * @Block(
 *   id = "degov_search_content_solr_filter",
 *   admin_label = @Translation("deGov search content Solr filters")
 * )
 */
class DegovSearchContentFilter extends BlockBase {
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
    $form['filter'] = array(
      '#type' => 'details',
      '#title' => t('Filter search results'),
      '#open' => TRUE,
      '#attributes' => ['class' => ['block-degov-search-content-filter']],
    );

    $ids = [
      'content_bundles',
      'tags',
      'search_content_topic',
      'changed'
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
