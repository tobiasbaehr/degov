<?php

namespace degov\Scripts\Robo;

use degov\Scripts\Robo\Exception\WrongFolderLocation;

class ProjectStructure extends \Robo\Tasks {

  /**
   * @var string
   */
  private $rootFolderLocation;

  public function __construct(string $rootFolderLocation) {
    $this->rootFolderLocation = $rootFolderLocation;
  }

  public function checkCorrectProjectStructure(string $distro): void {
    $this->checkBaseDistroFolder();
    $this->checkDistroFolder($distro);
    $this->checkDocrootFolder();
  }

  private function checkDocrootFolder(): void {
    $command = 'ls ' . $this->rootFolderLocation . ' | grep docroot';
    exec($command, $output);
    if (empty($output)) {
      throw new WrongFolderLocation('docroot folder is in wrong location.');
    }
  }

  private function checkBaseDistroFolder(): void {
    $this->checkDistroFolder('degov');
  }

  private function checkDistroFolder(string $distro): void {
    $command = 'ls ' . $this->rootFolderLocation . '/docroot/profiles/contrib | grep ' . $distro;
    exec($command, $output);
    if (empty($output)) {
      throw new WrongFolderLocation($distro . ' folder is in wrong location.');
    }
  }

}
