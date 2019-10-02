<?php

namespace Drupal\node_action;

use Drupal\Core\Url;


class UrlFactory {

  public function create($route_name, $route_parameters = [], $options = []): Url {
    return new Url($route_name, $route_parameters, $options);
  }

}
