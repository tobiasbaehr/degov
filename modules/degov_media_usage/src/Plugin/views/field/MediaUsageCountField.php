<?php

declare(strict_types=1);

namespace Drupal\degov_media_usage\Plugin\views\field;

use Drupal\Core\Url;
use Drupal\media\MediaInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Class MediaUsageCountField.
 *
 * @package Drupal\degov_media_usage\Plugin\views\field
 *
 * @ingroup views_field_handlers
 * @ViewsField("media_usage")
 */
class MediaUsageCountField extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  public function query() {}

  /**
   * Render a given ResultRow.
   *
   * @param \Drupal\views\ResultRow $values
   *   The values to render.
   *
   * @return array|null
   *   The finished render array.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function render(ResultRow $values): ?array {
    $media = $values->_entity;

    if ($media instanceof MediaInterface) {
      /** @var \Drupal\degov_media_usage\Service\MediaUsageInfo $info */
      $info = \Drupal::service('degov_media_usage.reference_info');
      $result = $info->getRefsCount($media);
      $url = Url::fromRoute(
        'entity.media.degov_media_usage_refs',
        ['media' => $media->id()]
      )->toString();

      return [
        '#markup' => '<span class="media-usage"><a href="' . $url . '">' . \Drupal::translation()->formatPlural($result, '@count place', '@count places', ['@count' => $result]) . '</a></span>',
      ];
    }

    return NULL;
  }

}
