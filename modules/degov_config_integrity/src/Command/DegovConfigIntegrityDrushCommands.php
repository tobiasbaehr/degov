<?php

namespace Drupal\degov_config_integrity\Command;

use Drupal\degov_config_integrity\DegovModuleIntegrityChecker;
use Drush\Commands\DrushCommands;

/**
 */
class DegovConfigIntegrityDrushCommands extends DrushCommands {

  /**
   * The module integrity checker.
   *
   * @var \Drupal\degov_config_integrity\DegovModuleIntegrityChecker
   */
  private $moduleIntegrityChecker;

  /**
   * DegovConfigIntegrityDrushCommands constructor.
   *
   * @param \Drupal\degov_config_integrity\DegovModuleIntegrityChecker $moduleIntegrityChecker
   */
  public function __construct(DegovModuleIntegrityChecker $moduleIntegrityChecker) {
    parent::__construct();
    $this->moduleIntegrityChecker = $moduleIntegrityChecker;
  }

  /**
   * Checks for missing configuration.
   *
   * @command config:diff:installed-modules
   */
  public function checkConfigOfInstalledModules() {
    drush_print(t('deGov Configuration Integrity Check running…'));
    $configurationIntegrityIntact = TRUE;
    foreach ($this->moduleIntegrityChecker->checkIntegrity() as $index => $module) {
      foreach ($module as $key => $messages) {
        drush_print(t('Module @module', ['@module' => $key]), 2);
        drush_print(t('Configuration is missing'), 4);
        foreach($messages as $message) {
          drush_print($message, 6);
        }
        $configurationIntegrityIntact = FALSE;
      }
    }
    if($configurationIntegrityIntact) {
      drush_print(t('All expected configuration seems to be in place.'));
    }
  }

}
