<?php

use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\media\Entity\Media;

/**
 * Migrate field_image_date to field_media_publish_date.
 */
function degov_media_image_post_update_migrate_field_date(&$sandbox) {

  $oldFieldName = 'field_image_date';
  $newFieldName = 'field_media_publish_date';

  // Initialize some variables during the first pass through.
  if (!isset($sandbox['total'])) {
    $max = \Drupal::entityQuery('media')
      ->condition('bundle', 'image')
      ->count()
      ->execute();
    $sandbox['total'] = $max;
    $sandbox['current'] = 0;
  }

  $batchSize = 50;

  // Handle one pass through.
  $Ids = \Drupal::entityQuery('media')
    ->condition('bundle', 'image')
    ->range($sandbox['current'], $batchSize)
    ->execute();
  $medias = \Drupal\media\Entity\Media::loadMultiple($Ids);
  foreach ($medias as $media) {
    /**
     * @var $media Media
     */

    if ($media->hasField($oldFieldName)) {
      $imageDate = (new \DateTime($media->get($oldFieldName)->value))
        ->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);
      $imageCreatedDate = \Drupal\Core\Datetime\DrupalDateTime::createFromTimestamp($media->get('created')->value)
        ->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);
      if ($media->get($newFieldName)->isEmpty()) {
        $imagePublishedDate = $media->get($oldFieldName)->value === NULL ? $imageCreatedDate : $imageDate;
        $media->set($newFieldName, $imagePublishedDate);
        $media->set($oldFieldName, NULL);
        $media->save();
      }
    }
    $sandbox['current']++;
  }

  $sandbox['#finished'] = ($sandbox['current'] / $sandbox['total']);

  return t('@current media processed.', ['@current' => $sandbox['current']]);
}
