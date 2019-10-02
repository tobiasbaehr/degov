<?php

namespace Drupal\node_action;

use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;


class Redirector {

  private $messenger;

  private $redirectResponseFactory;

  private $urlFactory;

  public function __construct(MessengerInterface $messenger, RedirectResponseFactory $redirectResponseFactory, UrlFactory $urlFactory) {
    $this->messenger = $messenger;
    $this->redirectResponseFactory = $redirectResponseFactory;
    $this->urlFactory = $urlFactory;
  }

  public function computeRedirectResponseByEntities(array $entities, string $routeName): ?RedirectResponse {
    $entityIds = [];

    foreach ($entities as $entity) {
      $entityIds[$entity->id()] = $entity->getTitle();
    }

    if (\count($entityIds) < 1) {
      $this->removeDefaultMessage();
      return NULL;
    }

    return $this->computeRedirectResponse($routeName, ['entityIds' => $entityIds]);
  }

  private function removeDefaultMessage(): void {
    $this->messenger->deleteByType('error');
  }

  public function computeRedirectResponse(string $routeName, array $routeParameters = []): Response {
    $redirectUrl = $this->urlFactory->create($routeName, $routeParameters);
    $response = $this->redirectResponseFactory->create($redirectUrl->toString());
    return $response->send();
  }

}
