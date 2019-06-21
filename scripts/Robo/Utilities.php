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

  public static function determineLatestReleaseDevBranch(string $branches): string {
    $allBranches = explode(' ', $branches);
    $branchVersionCombinations = [];

    foreach ($allBranches as $branch) {
      $version = preg_replace('/[^0-9]/', '', $branch);
      if (is_numeric(strpos($branch, 'remotes/origin/release/')) && is_numeric(strpos($branch, '-dev'))) {
        if (\strlen($version) > 2) {
          $version = substr($version, 0, 2);
        }
        $branchVersionCombinations[$version] = $branch;
      }
    }

    ksort($branchVersionCombinations);

    return array_pop($branchVersionCombinations);
  }

}
