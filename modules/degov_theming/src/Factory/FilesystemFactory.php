<?php

namespace Drupal\degov_theming\Factory;

use Symfony\Component\Filesystem\Filesystem;

class FilesystemFactory {

  /**
   * @return Filesystem
   */
  public function create() {
    return new Filesystem();
  }

}
