<?php

namespace degov\Scripts\Robo;

use degov\Scripts\Robo\Exception\WrongFolderLocation;

class ConsoleReturn extends \Robo\Tasks {

  public function isFolderLocation(string $command): bool {
    $message = $this->taskExecStack()
      ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_DEBUG)
      ->exec($command)
      ->run()
      ->getMessage();
    if (!empty($message)) {
      return TRUE;
    } else {
      throw new WrongFolderLocation('docroot folder is in wrong location.');
    }
  }

  public function isApplicationInstalled(string $command): bool {
    exec($command, $output);
    if (!empty($output)) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

}
