<?php

namespace degov\Scripts\Robo;

use degov\Scripts\Robo\Exception\ApplicationRequirementFail;
use degov\Scripts\Robo\Exception\WrongFolderLocation;
use Robo\Contract\VerbosityThresholdInterface;
use Symfony\Component\Yaml\Yaml;


/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{

  use RunsTrait;

  public function degovUpdate(): void {
    $this->checkRequirements();

    $this->runComposerUpdate();

    $this->runTranslationsUpdate();

    $this->runDrupalUpdateHooks();

    $this->runEntityUpdates();

    $this->runConfigurationExportIntoFilesystem();

    $this->runBaseThemeNpmPackageUpdate();

    $this->runCustomThemeUpdate();

    $this->say('Congratulation! Finished nrwGOV update.');
  }

}
