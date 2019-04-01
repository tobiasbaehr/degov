<?php

namespace degov\Scripts\Robo;

use degov\Scripts\Robo\Exception\WrongFolderLocation;

class ProjectStructure extends \Robo\Tasks {

  public function isCorrectProjectStructure(string $distro = 'degov'): void {
    $this->checkBaseDistroFolder();
    $this->checkDistroFolder($distro);
    $this->checkDocrootFolder();
  }

  private function checkDocrootFolder(): bool {
    $command = 'ls ../../../../../../ | grep docroot';
    exec($command, $output);
    if (!empty($output)) {
      return TRUE;
    } else {
      throw new WrongFolderLocation('docroot folder is in wrong location.');
    }
  }

  private function checkBaseDistroFolder(): void {
    $this->checkDistroFolder('degov');
  }

  private function checkDistroFolder(string $distro): bool {
    $command = 'ls ../../../../../../docroot/profiles/contrib | grep ' . $distro;
    exec($command, $output);
    if (!empty($output)) {
      return TRUE;
    } else {
      throw new WrongFolderLocation($distro . ' folder is in wrong location.');
    }
  }

}
