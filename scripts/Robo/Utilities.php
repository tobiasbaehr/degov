<?php

namespace degov\Scripts\Robo;

use degov\Scripts\Robo\Exception\ApplicationRequirementFail;

class Utilities {

  public static function removeCliLineBreaks(string $output): string {
    return str_replace(PHP_EOL, '', $output);
  }

  /**
   * @param string $output
   *  The output of "node -v" cli command.
   *
   * @return bool
   * @throws ApplicationRequirementFail
   */
  public static function checkNodeVersion(string $output): void {
    $onlyVersionNumber = preg_replace('/[^0-9]/', '', $output);

    if (!(strpos($onlyVersionNumber, '6') === 0)) {
      throw new ApplicationRequirementFail('Aborting. Try again after fixing the NodeJS version requirement');
    }
  }

}
