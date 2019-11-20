<?php

namespace Drupal\entity_reference_timer\Service;

use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\Core\Database\Connection;

/**
 * Class EntityReferenceTimerCronService.
 *
 * @package Drupal\entity_reference_timer\Service
 */
class EntityReferenceTimerCronService {

  /**
   * @var \Drupal\Core\Cache\CacheTagsInvalidatorInterface
   */
  private $cacheTagsInvalidator;

  /**
   * @var \Drupal\Core\Database\Connection
   */
  private $database;

  /**
   * EntityReferenceTimerCronService constructor.
   *
   * @param \Drupal\Core\Cache\CacheTagsInvalidatorInterface $cacheTagsInvalidator
   * @param \Drupal\Core\Database\Connection $database
   */
  public function __construct(CacheTagsInvalidatorInterface $cacheTagsInvalidator, Connection $database) {
    $this->cacheTagsInvalidator = $cacheTagsInvalidator;
    $this->database = $database;
  }

  public function clearExpiredCaches(): void {
    $tagsToInvalidate = [];

    $cacheTagsResults = $this->database->select('cachetags', 'ct')
      ->fields('ct')
      ->range(0, 1)
      ->condition('expires', time(), '<=')
      ->execute();

    foreach ($cacheTagsResults as $cacheTagsResult) {
      $tagsToInvalidate[] = $cacheTagsResult->tag;
    }

    if (\count($tagsToInvalidate) > 0) {
      $this->database->update('cachetags')
        ->fields(['expires' => NULL])
        ->condition('tag', $tagsToInvalidate)
        ->execute();
      $this->cacheTagsInvalidator->invalidateTags($tagsToInvalidate);
    }
  }

}
