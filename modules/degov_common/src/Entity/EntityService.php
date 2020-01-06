<?php

namespace Drupal\degov_common\Entity;

/**
 * Class EntityService.
 */
class EntityService {

  /**
   * Load.
   */
  public function load(string $entityType, array $conditions) {
    $query = \Drupal::entityTypeManager()->getStorage($entityType)->getQuery();
    foreach ($conditions as $field => $value) {
      $query->condition($field, $value);
    }

    if ($nid = current($query->execute())) {
      return $nid;
    }
    return NULL;
  }

}
