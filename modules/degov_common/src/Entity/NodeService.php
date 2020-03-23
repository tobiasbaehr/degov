<?php

namespace Drupal\degov_common\Entity;

use Drupal\Core\Entity\EntityInterface;

/**
 * Class NodeService.
 */
class NodeService {

  /**
   * Load.
   */
  public function load(array $conditions): ?EntityInterface {
    return \Drupal::service('degov_common.entity')->load('node', $conditions);
  }

}
