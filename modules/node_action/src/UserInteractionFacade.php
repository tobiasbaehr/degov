<?php

namespace Drupal\node_action;

use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Session\AccountProxyInterface;


class UserInteractionFacade {

  public $currentUser;

  public $redirectResponseFactory;

  public $messenger;

  public $stringTranslationAdapter;

  public $urlFactory;

  public function __construct(AccountProxyInterface $currentUser, RedirectResponseFactory $redirectResponseFactory, MessengerInterface $messenger, StringTranslationAdapter $stringTranslationAdapter, UrlFactory $urlFactory) {
    $this->currentUser = $currentUser;
    $this->redirectResponseFactory = $redirectResponseFactory;
    $this->messenger = $messenger;
    $this->stringTranslationAdapter = $stringTranslationAdapter;
    $this->urlFactory = $urlFactory;
  }

}
