<?php

namespace Drupal\degov_common;

/**
 * Class DeGovModuleIntegrity
 *
 * @package Drupal\degov_common
 */
class DeGovModuleIntegrity {

  public function checkModule($moduleName): array {
    $missingConfiguration = [];
    if(strpos($moduleName,'degov') === FALSE) return $missingConfiguration;
    $files = file_scan_directory(drupal_get_path('module', $moduleName) . '/config/install', '/[a-z0-9_]*\.yml/');
    foreach ($files as $file) {
      $fileName = $file->filename;
      $configName = str_replace('.yml','', $fileName);
      if (empty(\Drupal::configFactory()->get($configName)->getRawData())) {
        $missingConfiguration[$moduleName][] = $configName;
      }
    }
    if(empty($missingConfiguration)) {
      $missingConfiguration[$moduleName] = (string)t('OK');
    }
    return $missingConfiguration;
  }
}
