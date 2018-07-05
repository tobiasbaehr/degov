<?php

namespace Drupal\degov_common\Entity;

use Drupal\node\Entity\Node;

class NodeService {

  public function load(array $conditions): ?Node {
    $query = \Drupal::entityTypeManager()->getStorage('node')->getQuery();
    foreach ($conditions as $field => $value) {
      $query->condition($field, $value);
    }

    if ($nid = current($query->execute())) {
      return Node::load($nid);
    }

    return NULL;
  }

}
