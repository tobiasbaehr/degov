<?php

namespace Drupal\node_action\AccessChecker;

use Drupal\Core\Entity\Entity;
use Drupal\node\Entity\Node;

/**
 * Interface ActionAbstractInterface.
 */
interface ActionAbstractInterface {

  /**
   * Has permission.
   */
  public function hasPermission(): bool;

  /**
   * Has permissions by term permission.
   */
  public function hasPermissionsByTermPermission(Node $node): bool;

  /**
   * Can access.
   */
  public function canAccess(Entity $entity);

}
