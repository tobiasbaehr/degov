<?php

namespace degov\Scripts\Robo;

use degov\Scripts\Robo\Exception\ApplicationRequirementFail;
use degov\Scripts\Robo\Exception\WrongFolderLocation;
use Robo\Contract\VerbosityThresholdInterface;
use Symfony\Component\Yaml\Yaml;

trait RunsTrait {

  private $rootFolderPath;

  protected function init(): void {
    $rootFolderAbsolutePath = $this->taskExecStack()
      ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_DEBUG)
      ->exec('pwd')
      ->printOutput(FALSE)
      ->run()
      ->getMessage();

    $this->rootFolderPath = $rootFolderAbsolutePath;
  }

  protected function runComposerUpdate(): void {
    $this->say('Proceeding with update.');
    $this->say('Updating dependencies via Composer..');

    $this->taskExecStack()
      ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_DEBUG)
      ->exec('cd ' . $this->rootFolderPath . '&& composer update nrwgov/nrwgov --with-dependencies')
      ->run();

    $this->say('Finished Composer update.');
  }

  protected function runTranslationsUpdate(): void {
    $this->say('Checking translation updates..');

    $this->taskExecStack()
      ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_DEBUG)
      ->exec('cd ' . $this->rootFolderPath . '&& drush locale-check')
      ->run();

    $this->say('Finished translation updates check..');

    $this->say('Updating translations..');

    $this->taskExecStack()
      ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_DEBUG)
      ->exec('cd ' . $this->rootFolderPath . '&& drush locale-update')
      ->run();

    $this->say('Finished translation update.');
  }

  protected function runDrupalUpdateHooks(): void {
    $this->say('Running Drupal update hooks.');

    $this->taskExecStack()
      ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_DEBUG)
      ->exec('cd ' . $this->rootFolderPath . '&& drush updb')
      ->run();

    $this->say('Finished running Drupal update hooks.');
  }

  protected function runEntityUpdates(): void {
    $this->say('Running entity updates.');

    $this->taskExecStack()
      ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_DEBUG)
      ->exec('cd ' . $this->rootFolderPath . '&& drush entup')
      ->run();

    $this->say('Finished running entity updates.');
  }

  protected function runConfigurationExportIntoFilesystem(): void {
    $this->say('Exporting configuration from storage into filesystem.');

    $this->taskExecStack()
      ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_DEBUG)
      ->exec('cd ' . $this->rootFolderPath . '&& drush cex')
      ->run();

    $this->say('Finished exporting configuration from storage into filesystem.');
  }

  protected function runBaseThemeNpmPackageUpdate(): void {
    $this->say('Installing NPM packages in NRW base theme.');

    $pathToNpm = $this->getCommandOutput('which npm');

    $this->taskExecStack()
      ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_DEBUG)
      ->exec('cd ' . $this->rootFolderPath . '&& cd docroot/themes/nrw/nrw_base_theme && ' . $pathToNpm . ' i')
      ->run();

    $this->say('Finished installation of NPM packages in NRW base theme.');
  }

  protected function runCustomThemeUpdate(): void {
    $this->say('Installing NPM packages and re-compiling JS/CSS assets in custom theme.');

    $pathToCustomThemeConfig = 'config' . DIRECTORY_SEPARATOR . 'sync' . DIRECTORY_SEPARATOR . 'system.theme.yml';
    if (file_exists($pathToCustomThemeConfig)) {
      $themeConfig = Yaml::parse($pathToCustomThemeConfig);
      if ($themeConfig['default'] !== 'nrw_base_theme') {
        $pathToNpm = $this->getCommandOutput('which npm');

        $this->_exec('cd ' . $this->rootFolderPath . '&& cd docroot/themes/custom/' . $themeConfig['default'] . ' && ' . $pathToNpm . ' i');
      }
      $this->say('Finished installation of NPM packages and re-compilation of JS/CSS assets in custom theme.');
    } else {
      $this->say('No custom theme detected. Bypassing installation of NPM packages and re-compilation of JS/CSS assets in custom theme.');
    }

  }

  protected function checkRequirements(string $distro = 'degov'): void {
    $projectStructure = new ProjectStructure($this->rootFolderPath);

    try {
      if ($projectStructure->checkCorrectProjectStructure($distro)) {
        $this->say('Project structure is correct.');
      }
    } catch (WrongFolderLocation $exception) {
      $this->say($exception->getMessage());
      throw new \Exception('Aborting update.');
    }

    $requiredApplications = new RequiredApplications();

    try {
      if ($requiredApplications->checkApplicationRequirementFulfilled()) {
        $this->say('Applications requirement is fulfilled.');
      }
    } catch (ApplicationRequirementFail $exception) {
      $this->say($exception->getMessage());
      throw new \Exception('Aborting update.');
    }
  }

  private function getCommandOutput(string $command, $customLocation = null): string {
    $taskExecStack = $this->taskExecStack()
      ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_DEBUG);

    if ($customLocation) {
      $taskExecStack = $taskExecStack
        ->exec('cd ' . $customLocation);
    }

    $commandOutput = $taskExecStack
      ->exec($command)
      ->printOutput(FALSE)
      ->run()
      ->getMessage();

    return Utilities::removeCliLineBreaks($commandOutput);
  }

  protected function newGitBranch($gitBranchLocation, $gitBranchName, $latestReleaseDevBranch = null): void {
    if (empty($latestReleaseDevBranch)) {
      $allBranches = $this->getCommandOutput('git branch --all', $gitBranchLocation);
      $latestReleaseDevBranch = Utilities::determineLatestReleaseDevBranch($allBranches);
    }

    $this->ensureCleanGitTree($gitBranchLocation);

    $taskGitStack = $this->taskExecStack();

    if ($gitBranchLocation) {
      $taskGitStack
        ->exec('cd ' . $gitBranchLocation);
    }

    $taskGitStack
      ->exec('git fetch')
      ->exec('git checkout -b ' . $gitBranchName . ' ' . $latestReleaseDevBranch)
      ->run();
  }

  private function isGitRemoteOrigin(string $folderLocation, string $gitRemoteOrigin): bool {
    $remoteInfo = $this->taskExecStack()
      ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_DEBUG)
      ->exec('cd ' . $folderLocation)
      ->exec('git remote show origin')
      ->printOutput(FALSE)
      ->run()
      ->getMessage();

    if (!is_numeric(strpos($remoteInfo, $gitRemoteOrigin))) {
      return FALSE;
    }

    return TRUE;
  }

  protected function ensureGitRepo(string $projectFolderLocation, string $gitRemoteOrigin, string $projectFolderName): void {
    if (!$this->isGitRemoteOrigin($projectFolderLocation, $gitRemoteOrigin)) {
      $this->say('Could not find remote origin. Cloning repository from '. $gitRemoteOrigin . '.');

      $this->taskExecStack()
        ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_DEBUG)
        ->exec('cd ' . $projectFolderLocation . '/..')
        ->exec('rm -rf ' . $projectFolderName)
        ->exec('git clone ' . $gitRemoteOrigin)
        ->run();

      if (!$this->isGitRemoteOrigin($projectFolderLocation, $gitRemoteOrigin)) {
        throw new \Exception('Could not find ' . $gitRemoteOrigin . ' remote origin, after Git clone try. Exiting.');
      }
      else {
        $this->say('Repository clone from ' . $gitRemoteOrigin . ' has been successful.');
      }
    }
  }

  private function ensureCleanGitTree($gitBranchLocation): void {
    $this->say('Check Git branch state.');
    $gitStatus = $this->getCommandOutput('git status', $gitBranchLocation);
    if (!is_numeric(strpos($gitStatus, 'working tree clean'))) {
      if (!$this->confirm('Your Git working tree is not clean. If you have no important changes, I can wipe the unsaved changes for you. Shall I do that?')) {
        throw new \Exception('Exiting because of not clean Git working tree. Commit the changes via Git or try "git clean -f" for wiping uncommited files or wipe staged files via "git reset HEAD --hard".');
      }

      $this->say('Resetting Git branch.');

      $taskExecStack = $this->taskExecStack()
        ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_DEBUG);

      if ($gitBranchLocation) {
        $taskExecStack
          ->exec('cd ' . $gitBranchLocation);
      }

      $taskExecStack
        ->exec('cd `git rev-parse --show-toplevel`')
        ->exec('git clean -f')
        ->exec('git reset HEAD --hard')
        ->run();

      $gitStatus = $this->getCommandOutput('git status', $gitBranchLocation);
      if (!is_numeric(strpos($gitStatus, 'working tree clean'))) {
        throw new \Exception('Git branch still not clean.');
      }
    }

    $this->say('Git branch is clean. Checking out new Git branch from latest release dev branch.');
  }

}
