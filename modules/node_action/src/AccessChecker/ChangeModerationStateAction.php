<?php

namespace Drupal\node_action\AccessChecker;

use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\node_action\StringTranslationAdapter;
use Drupal\permissions_by_term\Service\AccessCheck;

class ChangeModerationStateAction extends ActionAbstract implements AccessCheckInterface {

  use MessagesTrait;

  private static $permission = 'use change moderation state action';

  private static $actionName = 'Change moderation state';

  public function __construct(AccessCheck $accessCheck, AccountProxyInterface $currentUser, MessengerInterface $messenger, StringTranslationAdapter $stringTranslationAdapter) {
    parent::__construct($accessCheck, $currentUser, self::$permission, $messenger, self::$actionName, $stringTranslationAdapter);
  }

}
