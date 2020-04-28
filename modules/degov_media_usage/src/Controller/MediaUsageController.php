<?php

declare(strict_types=1);

namespace Drupal\degov_media_usage\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class MediaUsageController.
 *
 * @package Drupal\degov_media_usage\Controller
 */
final class MediaUsageController extends ControllerBase {

  /**
   * Turns an array of Media into a table of media reference info.
   *
   * @param array $media
   *   An array of Media entities.
   *
   * @return array
   *   A render array.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityMalformedException
   */
  public function referencesPage(array $media) {
    /** @var \Drupal\degov_media_usage\Service\MediaUsageInfo $info */
    $info = \Drupal::service('degov_media_usage.reference_info');

    $refs = [];
    foreach ($media as $mediaEntity) {
      $entityReferences = $info->getRefsList($mediaEntity);
      foreach ($entityReferences as $entityReference) {
        $refs[] = $entityReference;
      }
    }

    if (!$refs) {
      return [
        'description' => [
          '#prefix' => '<h4>',
          '#suffix' => '</h4>',
          '#markup' => $this->t('This media has no references.'),
        ],
      ];
    }

    $table = $info->buildRefsTable($refs);

    return [
      'description' => [
        '#prefix' => '<h4>',
        '#suffix' => '</h4>',
        '#markup' => $this->t(
          'This media is referenced at @count place(s).',
          ['@count' => count($refs)]
        ),
      ],
      'table' => $table,
    ];
  }

  /**
   * Get a translated title for one or more Media usages.
   *
   * @param \Drupal\media\MediaInterface|array $media
   *   A Media entity or an array of Media entities.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup|string
   *   The references title.
   */
  public function referencesTitle($media) {
    if (\is_array($media)) {
      $labels = array_map(function ($media) {
        return $media->label();
      }, $media);

      return t('Browse media "@media" references', ['@media' => implode(', ', $labels)]);
    }

    return t('Browse media "@media" references', ['@media' => $media->label()]);
  }

}
