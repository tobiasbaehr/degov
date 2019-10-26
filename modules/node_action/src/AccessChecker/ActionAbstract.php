<?php

namespace Drupal\node_action\AccessChecker;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityBase;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\node\Entity\Node;
use Drupal\node_action\StringTranslationAdapter;
use Drupal\permissions_by_term\Service\AccessCheck;


class ActionAbstract {

  use MessagesTrait;

  private $accessCheck;

  private $currentUser;

  private $permission;

  private $messenger;

  private static $actionName;

  private $stringTranslationAdapter;

  public function __construct(
    AccessCheck $accessCheck,
    AccountProxyInterface $currentUser,
    string $permission,
    MessengerInterface $messenger,
    string $actionName,
    StringTranslationAdapter $stringTranslationAdapter
  ) {
    $this->accessCheck = $accessCheck;
    $this->currentUser = $currentUser;
    $this->permission = $permission;
    $this->messenger = $messenger;
    self::$actionName = $actionName;
    $this->stringTranslationAdapter = $stringTranslationAdapter;
  }

  public function hasPermission(): bool {
    if (!$this->currentUser->hasPermission($this->permission)) {
      return FALSE;
    }

    return TRUE;
  }

  public function hasPermissionsByTermPermission(Node $node): bool {
    return $this->accessCheck->canUserAccessByNodeId($node->id());
  }

  public function canAccess(EntityBase $entity) {
    if (!$entity instanceof Node) {
      return AccessResult::neutral();
    }

    if (!$this->hasPermission()) {
      $this->messenger->addMessage($this->stringTranslationAdapter->t(self::$messageNoRolePermission, ['@actionName' => self::$actionName, '@nodeTitle' => $entity->getTitle()]), 'warning', FALSE);
      return AccessResult::forbidden();
    }

    if (!$this->hasPermissionsByTermPermission($entity)) {
      $this->messenger->addMessage($this->stringTranslationAdapter->t(self::$messageNoPermissionByTermPermission, ['@actionName' => self::$actionName, '@nodeTitle' => $entity->getTitle()]), 'warning', FALSE);
      return AccessResult::forbidden();
    }

    return AccessResult::allowed();
  }

}
