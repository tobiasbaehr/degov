<?php

namespace Drupal\node_action\AccessChecker;

use Drupal\Core\Entity\EntityBase;


interface AccessCheckInterface {

  public function canAccess(EntityBase $entity);

}
