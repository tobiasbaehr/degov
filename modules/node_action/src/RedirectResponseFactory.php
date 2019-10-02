<?php

namespace Drupal\node_action;

use Symfony\Component\HttpFoundation\RedirectResponse;


class RedirectResponseFactory {

  public function create(string $url, $status = 302, array $headers = []): RedirectResponse {
    return new RedirectResponse($url, $status, $headers);
  }

}
