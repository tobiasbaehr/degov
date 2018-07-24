<?php

namespace Drupal\degov_common\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\node\Entity\Node;

class EntityService {

  public function load(string $entityType, array $conditions): ?EntityInterface {
    $query = \Drupal::entityTypeManager()->getStorage($entityType)->getQuery();
    foreach ($conditions as $field => $value) {
      $query->condition($field, $value);
    }

    if ($nid = current($query->execute())) {
      return Node::load($nid);
    }
    return NULL;
  }
}
