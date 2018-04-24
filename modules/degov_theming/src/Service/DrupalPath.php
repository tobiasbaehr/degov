<?php

namespace Drupal\degov_theming\Service;

class DrupalPath {

  public function getPath(string $type, string $name) {
    return drupal_get_path($type, $name);
  }

}