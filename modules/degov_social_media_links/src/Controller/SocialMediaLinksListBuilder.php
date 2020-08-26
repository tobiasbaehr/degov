<?php

namespace Drupal\degov_social_media_links\Controller;

use Drupal\Core\Config\Entity\DraggableListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Component\Render\FormattableMarkup;

/**
 * Configure social media links settings.
 */
class SocialMediaLinksListBuilder extends DraggableListBuilder {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'social_media_links_settings_list_builder_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header = [];
    $header['label'] = $this->t('label');
    $header['url'] = $this->t('url');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row = [];
    $row['icon'] = [
      '#markup' => new FormattableMarkup(
        '<div><i class="fa fa-lg @iconClass"></i>&nbsp;@label</div>', [
          '@label' => $entity->get('label'),
          '@iconClass' => $entity->get('icon'),
        ]
      ),
    ];
    $row['url'] = ['#markup' => $entity->get('url')];
    return $row + parent::buildRow($entity);
  }

  /**
   * Get render array with template.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   *
   * @return array
   *   Drupal render array
   */
  public static function getRenderArray() {
    $links = [];
    foreach (\Drupal::entityTypeManager()->getStorage('degov_social_media_links')->loadMultiple() as $item) {
      $links[] = $item->toArray();
    }
    uasort($links, ['\Drupal\Component\Utility\SortArray', 'sortByWeightElement']);
    return [
      '#theme' => 'social_media_links',
      '#content' => $links,
    ];
  }

}
