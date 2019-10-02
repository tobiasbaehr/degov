<?php

namespace Drupal\node_action\AccessChecker;

use Drupal\Core\Entity\Entity;
use Drupal\node\Entity\Node;


interface ActionAbstractInterface {

  public function hasPermission(): bool;

  public function hasPermissionsByTermPermission(Node $node): bool;

  public function canAccess(Entity $entity);

}
