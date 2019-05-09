<?php

namespace degov\Scripts\Robo;

use degov\Scripts\Robo\Exception\ApplicationRequirementFail;

class RequiredApplications {

  public function checkApplicationRequirementFulfilled(): void {
    if (!$this->isNpmInstalled() || !$this->isDrushInstalled()) {
      throw new ApplicationRequirementFail();
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
