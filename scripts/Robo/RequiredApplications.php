<?php

namespace degov\Scripts\Robo;

class RequiredApplications {

  public function isApplicationRequirementFulfilled(): bool {
    if ($this->isNpmInstalled() && $this->isDrushInstalled()) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  private function isNpmInstalled(): bool {
    $command = 'which npm';
    exec($command, $output);
    if (!empty($output)) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  private function isDrushInstalled(): bool {
    $command = 'which drush';
    exec($command, $output);
    if (!empty($output)) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

}
