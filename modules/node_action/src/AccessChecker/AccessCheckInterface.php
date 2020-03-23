<?php

namespace Drupal\node_action\AccessChecker;

use Drupal\Core\Entity\EntityBase;

/**
 * Class AccessCheckInterface.
 */
interface AccessCheckInterface {

  /**
   * Can access.
   */
  public function canAccess(EntityBase $entity);

}
