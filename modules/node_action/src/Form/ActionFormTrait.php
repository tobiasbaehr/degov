<?php

namespace Drupal\node_action\Form;


use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

trait ActionFormTrait {

  private function putTogetherHTMLList($entityIds): string {
    $nodesList = '<ul>';

    foreach ($entityIds as $entityId => $entityTitle) {
      $nodesList .= '<li><a href="/node/' . $entityId . '" title="' . $entityTitle . '">' . $entityTitle . '</a></li>';
    }

    $nodesList .= '</ul>';
    return $nodesList;
  }

  private function removeMessageFromDefaultAction(): void {
    $this->messenger()->deleteByType('error');
    $this->messenger()->deleteByType('status');
  }

  private function redirectToContentOverview(): RedirectResponse {
    $redirect_url = new Url('system.admin_content');
    $response = new RedirectResponse($redirect_url->toString());
    $response->send();

    return $response;
  }

}
