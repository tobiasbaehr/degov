<?php

namespace Drupal\degov_common;

use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Class DeGovModuleIntegrity
 *
 * @package Drupal\degov_common
 */
class DeGovModuleIntegrity {

  /**
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  private $moduleHandler;

  public function __construct(ModuleHandlerInterface $moduleHandler) {
    $this->moduleHandler = $moduleHandler;
  }

  public function checkModule($moduleName): array {
    $missingConfiguration = [];
    if (strpos($moduleName, 'degov') === FALSE) {
      return $missingConfiguration;
    }
    $files = file_scan_directory(drupal_get_path('module', $moduleName) . '/config/install', '/[a-z0-9_]*\.yml/');
    foreach ($files as $file) {
      $fileName = $file->filename;
      $configName = str_replace('.yml', '', $fileName);
      if (empty(\Drupal::configFactory()->get($configName)->getRawData())) {
        $missingConfiguration[$moduleName][] = $configName;
      }
    }

    return $missingConfiguration;
  }

  public function buildMessage($messages): string {
    $messageString = '';
    foreach ($messages as $message) {
      $messageString .= $message . ' ';
    }
    return $messageString;
  }

  public function checkIntegrity(): array {
    $messages = [];
    /**
     * @var $moduleHandler \Drupal\Core\Extension\ModuleHandler
     */
    $moduleHandler = \Drupal::service('module_handler');
    $modules = $this->moduleHandler->getModuleList();

    foreach ($modules as $module) {
      $messages[] = $this->checkModule($module->getName());
    }

    return $messages;
  }
}
