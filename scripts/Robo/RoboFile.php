<?php

use degov\Scripts\Robo\RunsTrait;

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

    $this->say('Congratulations! Finished the deGov update.');
  }

}
