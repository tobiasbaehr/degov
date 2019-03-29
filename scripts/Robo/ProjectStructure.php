<?php

namespace degov\Scripts\Robo;

use degov\Scripts\Robo\Exception\WrongFolderLocation;

class ProjectStructure extends \Robo\Tasks {

  public function isCorrectProjectStructure(): bool {
    if ($this->isDeGovFolder() && $this->isDocrootFolder() && $this->isNrwGovFolder()) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  private function isDocrootFolder(): bool {
    $command = 'ls ../../../../../../ | grep docroot';
    exec($command, $output);

    if (!empty($output)) {
      return TRUE;
    } else {
      throw new WrongFolderLocation('docroot folder is in wrong location.');
    }
  }

  private function isDeGovFolder(): bool {
    $command = 'ls ../../../../../../docroot/profiles/contrib | grep degov';
    exec($command, $output);
    if (!empty($output)) {
      return TRUE;
    } else {
      throw new WrongFolderLocation('deGov folder is in wrong location.');
    }
  }

  private function isNrwGovFolder(): bool {
    $command = 'ls ../../../../../../docroot/profiles/contrib | grep nrwgov';
    exec($command, $output);
    if (!empty($output)) {
      return TRUE;
    } else {
      throw new WrongFolderLocation('nrwGOV folder is in wrong location.');
    }
  }

}
