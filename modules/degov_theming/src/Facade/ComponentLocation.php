<?php

namespace Drupal\degov_theming\Facade;

use Drupal\Core\Asset\LibraryDiscovery;
use Drupal\degov_theming\Factory\FilesystemFactory;
use Drupal\degov_theming\Service\DrupalPath;
use Symfony\Component\Filesystem\Filesystem;

class ComponentLocation {

  /**
   * @var LibraryDiscovery
   */
  private $libraryDiscovery;

  /**
   * @var DrupalPath
   */
  private $drupalPath;

  /**
   * @var Filesystem
   */
  private $filesystem;

  public function __construct(LibraryDiscovery $libraryDiscovery, FilesystemFactory $filesystemFactory, DrupalPath $drupalPath) {
    $this->libraryDiscovery = $libraryDiscovery;
    $this->filesystem = $filesystemFactory;
    $this->drupalPath = $drupalPath;
  }

  public function getLibraryDiscovery() {
    return $this->libraryDiscovery;
  }

  public function getFilesystem() {
    return $this->filesystem->create();
  }

  public function getDrupalPath() {
    return $this->drupalPath;
  }

}
