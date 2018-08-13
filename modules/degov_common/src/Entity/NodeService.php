<?php

namespace Drupal\degov_common\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\node\Entity\Node;

class NodeService {

  public function load(array $conditions): ?EntityInterface {
    return \Drupal::service('degov_common.entity')->load('node', $conditions);
  }
}
