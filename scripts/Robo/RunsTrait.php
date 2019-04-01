<?php

namespace degov\Scripts\Robo;

use degov\Scripts\Robo\Exception\ApplicationRequirementFail;
use degov\Scripts\Robo\Exception\WrongFolderLocation;
use Robo\Contract\VerbosityThresholdInterface;
use Symfony\Component\Yaml\Yaml;

trait RunsTrait {

  private $rootFolder = '../../../../../../';

  protected function runComposerUpdate(): void {
    $this->say('Proceeding with update.');
    $this->say('Updating dependencies via Composer..');

    $this->taskExecStack()
      ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_DEBUG)
      ->exec('cd ' . $this->rootFolder . '&& composer update nrwgov/nrwgov --with-dependencies')
      ->run();

    $this->say('Finished Composer update.');
  }

  protected function runTranslationsUpdate(): void {
    $this->say('Checking translation updates..');

    $this->taskExecStack()
      ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_DEBUG)
      ->exec('cd ' . $this->rootFolder . '&& drush locale-check')
      ->run();

    $this->say('Finished translation updates check..');

    $this->say('Updating translations..');

    $this->taskExecStack()
      ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_DEBUG)
      ->exec('cd ' . $this->rootFolder . '&& drush locale-update')
      ->run();

    $this->say('Finished translation update.');
  }

  protected function runDrupalUpdateHooks(): void {
    $this->say('Running Drupal update hooks.');

    $this->taskExecStack()
      ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_DEBUG)
      ->exec('cd ' . $this->rootFolder . '&& drush updb')
      ->run();

    $this->say('Finished running Drupal update hooks.');
  }

  protected function runEntityUpdates(): void {
    $this->say('Running entity updates.');

    $this->taskExecStack()
      ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_DEBUG)
      ->exec('cd ' . $this->rootFolder . '&& drush entup')
      ->run();

    $this->say('Finished running entity updates.');
  }

  protected function runConfigurationExportIntoFilesystem(): void {
    $this->say('Exporting configuration from storage into filesystem.');

    $this->taskExecStack()
      ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_DEBUG)
      ->exec('cd ' . $this->rootFolder . '&& drush cex')
      ->run();

    $this->say('Finished exporting configuration from storage into filesystem.');
  }

  protected function runBaseThemeNpmPackageUpdate(): void {
    $this->say('Installing NPM packages in NRW base theme.');

    $pathToNpm = $this->_exec('which npm')->getMessage();

    $this->taskExecStack()
      ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_DEBUG)
      ->exec('cd ' . $this->rootFolder . '&& cd docroot/themes/nrw/nrw_base_theme && ' . $pathToNpm . ' i')
      ->run();

    $this->say('Finished installation of NPM packages in NRW base theme.');
  }

  protected function runCustomThemeUpdate(): void {
    $this->say('Installing NPM packages and re-compiling JS/CSS assets in custom theme.');

    $pathToCustomThemeConfig = 'config' . DIRECTORY_SEPARATOR . 'sync' . DIRECTORY_SEPARATOR . 'system.theme.yml';
    if (file_exists($pathToCustomThemeConfig)) {
      $themeConfig = Yaml::parse($pathToCustomThemeConfig);
      if ($themeConfig['default'] !== 'nrw_base_theme') {
        $pathToNpm = $this->_exec('which npm')->getMessage();
        $this->_exec('cd ' . $this->rootFolder . '&& cd docroot/themes/custom/' . $themeConfig['default'] . ' && ' . $pathToNpm . ' i');
      }
      $this->say('Finished installation of NPM packages and re-compilation of JS/CSS assets in custom theme.');
    } else {
      $this->say('No custom theme detected. Bypassing installation of NPM packages and re-compilation of JS/CSS assets in custom theme.');
    }

  }

  protected function checkRequirements(string $distro = 'degov'): void {
    $projectStructure = new ProjectStructure();

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

}
