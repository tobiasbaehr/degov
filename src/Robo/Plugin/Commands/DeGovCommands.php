<?php

namespace Drupal\degov\Robo\Plugin\Commands;

use Drupal\Core\Serialization\Yaml;
use Robo\Tasks;
use degov\Scripts\Robo\Model\InstallationProfile;
use degov\Scripts\Robo\Model\InstallationProfileCollection;
use degov\Scripts\Robo\RunsTrait;
use Symfony\Component\Finder\Finder;

/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class DeGovCommands extends Tasks {

  use RunsTrait;

  /**
   * New DeGov issue.
   *
   * @param string $gitBranchName
   *   Git branch name.
   *
   * @command degov:new-issue
   *
   * @throws \Exception
   */
  public function degovNewIssue(string $gitBranchName): void {
    $this->init();
    $degovFolderLocation = $this->rootFolderPath . '/docroot/profiles/contrib/degov';
    $this->ensureGitRepo($degovFolderLocation, 'git@bitbucket.org:publicplan/degov.git', 'degov');
    $this->newGitBranch($degovFolderLocation, $gitBranchName);
  }

  /**
   * Allows you to install or reinstall deGOV.
   *
   * @throws \degov\Scripts\Robo\Exception\NoInstallationProfileProvided
   */
  public function degovSiteInstall(): void {
    $installationProfileCollection = new InstallationProfileCollection();

    $mainInstallationProfile = new InstallationProfile(
      'degov',
      'deGov',
      'deGov - Drupal 8 for Government'
    );

    $installationProfileCollection->setMainInstallationProfile($mainInstallationProfile);
    $this->runDrushProfileInstallation($installationProfileCollection);
  }

  /**
   * Normalize info files.
   *
   * @param string $path Path to a directory with info files.
   * @param string $package Package name in case non is set.
   */
  public function degovNormalizeInfoFiles(string $path, string $package = 'deGov') {
    $finder = new Finder();
    $files = $finder->files()->in(trim($path, DIRECTORY_SEPARATOR))->name('*.info.yml');
    foreach ($files as $file) {
      $yaml = Yaml::decode(file_get_contents($file->getPathname()));
      $new_yaml = ['name' => $yaml['name']];
      unset($yaml['name']);
      $new_yaml += ['type' => $yaml['type']];
      unset($yaml['type']);

      if (!empty($yaml['description'])) {
        $new_yaml += ['description' => trim(str_replace('\"', '"', $yaml['description']))];
        unset($yaml['description']);
      }

      if (empty($yaml['package'])) {
        $yaml['package'] = $package;
      }
      $new_yaml += ['package' => $yaml['package']];
      unset($yaml['package']);
      $new_yaml += ['core_version_requirement' => '^9'];
      unset($yaml['core_version_requirement']);
      unset($yaml['core']);
      $new_yaml += $yaml;
      file_put_contents($file->getPathname(), Yaml::encode($new_yaml));
    }
  }

}
