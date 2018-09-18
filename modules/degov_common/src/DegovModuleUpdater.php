<?php

namespace Drupal\degov_common;


use Drupal\config_replace\ConfigReplacer;
use Symfony\Component\Yaml\Yaml;

/**
 * Class DegovModuleUpdater
 *
 * @package Drupal\degov_common
 */
class DegovModuleUpdater extends ConfigReplacer {

  public function applyUpdates($module, $version) {
    $source_dir = drupal_get_path('module', $module) . '/config/update_' . $version;
    $this->manageConfig($module, $source_dir);
  }

  public function reImport($configurationName, $moduleName, $folderName) {
    $parsedConfiguration = Yaml::parseFile(drupal_get_path('module', $moduleName) . "/config/$folderName/$configurationName.yml");
    $this->configFactory->getEditable($configurationName)
      ->setData($parsedConfiguration)
      ->save();
  }

  public function onModuleInstalled($module, $installed_module) {
    $source_dir = drupal_get_path('module', $module) . '/config/' . $installed_module;
    $this->manageConfig($module, $source_dir);
  }

  private function manageConfig($module, $source_dir) {
    if (file_exists($source_dir)) {
      // Are there any new installs?
      $install_dir = $source_dir . '/install';
      if (file_exists($install_dir)) {
        \Drupal::service('degov_config.updater')->checkConfigurationChanges($install_dir);
      }
      // Are there any optional?
      $optional_dir = $source_dir . '/optional';
      if (file_exists($optional_dir)) {
        \Drupal::service('degov_config.updater')->checkOptional($optional_dir);
      }
      // Are there any blocks?
      $blocks_dir = $source_dir .'/block';
      if (file_exists($blocks_dir)) {
        \Drupal::service('degov_config.block_installer')->installBlockConfig($blocks_dir);
      }
      // Are there any rewrites?
      $updates_dir = $source_dir .'/rewrite';
      if (file_exists($updates_dir)) {
        $extension = $this->moduleHandler->getModule($module);
        $this->rewriteDirectoryConfig($extension, $updates_dir);
      }
    }
  }

}
