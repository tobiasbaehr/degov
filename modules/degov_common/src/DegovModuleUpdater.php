<?php

namespace Drupal\degov_common;

use Drupal\config_replace\ConfigReplacer;

/**
 * Class DegovModuleUpdater.
 *
 * @package Drupal\degov_common
 */
class DegovModuleUpdater extends ConfigReplacer {

  /**
   * Applies all config updates for the version.
   *
   * @param string $module
   *   The module name.
   * @param string $version
   *   The update version.
   */
  public function applyUpdates(string $module, string $version): void {
    $source_dir = drupal_get_path('module', $module) . '/config/update_' . $version;
    $this->manageConfig($module, $source_dir);
  }

  /**
   * Imports a single config file.
   *
   * @param string $config_name
   *   The configuration name without extention.
   * @param string $module
   *   The module name.
   * @param string $config_type
   *   The configuration type, this could be install, optional or block.
   * @param bool $force
   *   Set to TRUE to import the configuration without checking if the module
   *   installed.
   */
  public function reImport(string $config_name, string $module, string $config_type, bool $force = FALSE) : void {
    if ($force || \Drupal::moduleHandler()->moduleExists($module)) {
      /** @var \Drupal\degov_common\DegovConfigUpdate $updater */
      $updater = \Drupal::service('degov_config.updater');
      $updater->importConfigFile($module, $config_name, $config_type);
    }
  }

  /**
   * Import config file.
   *
   * @deprecated Use reImport.
   */
  public function importConfigFile(string $ymlConfigFilename, string $moduleName, string $folderName, bool $force = FALSE) : void {
    if (substr($ymlConfigFilename, -4) !== '.yml') {
      throw new \Exception('Config file must be a yml file. Given config filename is not ending with ".yml".');
    }

    $configurationName = substr_replace($ymlConfigFilename, '', -4);
    $this->reImport($configurationName, $moduleName, $folderName, $force);
  }

  /**
   * On module installed.
   */
  public function onModuleInstalled($module, $installed_module): void {
    $source_dir = drupal_get_path('module', $module) . '/config/' . $installed_module;
    $this->manageConfig($module, $source_dir);
  }

  /**
   * Dispatcher to import configuration.
   *
   * @param string $module
   *   The module name.
   * @param string $source_dir
   *   A directory path to use for reading of configuration files.
   */
  private function manageConfig(string $module, string $source_dir): void {
    if (file_exists($source_dir)) {
      // Are there any new installs?
      $install_dir = $source_dir . '/install';
      /** @var \Drupal\degov_common\DegovConfigUpdate $updater */
      $updater = \Drupal::service('degov_config.updater');
      if (file_exists($install_dir)) {
        $updater->importConfigFiles($install_dir);
      }
      // Are there any optional?
      $optional_dir = $source_dir . '/optional';
      if (file_exists($optional_dir)) {
        $updater->checkOptional($optional_dir);
      }
      // Are there any blocks?
      $blocks_dir = $source_dir . '/block';
      if (file_exists($blocks_dir)) {
        \Drupal::service('degov_config.block_installer')->installBlockConfig($blocks_dir);
      }
      // Are there any rewrites?
      $updates_dir = $source_dir . '/rewrite';
      if (file_exists($updates_dir)) {
        $extension = $this->moduleHandler->getModule($module);
        $this->rewriteDirectoryConfig($extension, $updates_dir);
      }
    }
  }

  /**
   * Apply rewrites.
   */
  public function applyRewrites(string $module, string $version): void {
    $rewriteDir = drupal_get_path('module', $module) . '/config/update_' . $version . '/rewrite';
    if (file_exists($rewriteDir)) {
      $extension = $this->moduleHandler->getModule($module);
      $this->rewriteDirectoryConfig($extension, $rewriteDir);
    }
  }

}
