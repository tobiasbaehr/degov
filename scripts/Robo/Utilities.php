<?php

namespace degov\Scripts\Robo;

use degov\Scripts\Robo\Exception\ApplicationRequirementFail;

class Utilities {

  public static function removeCliLineBreaks(string $output): string {
    return str_replace(PHP_EOL, '', $output);
  }

  /**
   * @param string $version
   *  The output of "node -v" cli command.
   *
   * @return void
   * @throws ApplicationRequirementFail
   */
  public static function checkNodeVersion(string $version): void {
    $versionParts = explode('.', $version);
    $nodeVersion = (int) preg_replace('/\D/', '', $versionParts[0]);

    if ($nodeVersion < 8) {
      throw new ApplicationRequirementFail('Aborting. Make sure you are using a maintained NodeJS version. See https://nodejs.org/en/about/releases/.');
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
