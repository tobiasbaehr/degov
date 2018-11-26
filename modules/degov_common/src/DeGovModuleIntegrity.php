<?php

namespace Drupal\degov_common;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Class DeGovModuleIntegrity.
 *
 * @package Drupal\degov_common
 */
class DeGovModuleIntegrity {

  /**
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  private $moduleHandler;

  /**
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  private $configFactory;

  public function __construct(ModuleHandlerInterface $moduleHandler, ConfigFactoryInterface $configFactory) {
    $this->moduleHandler = $moduleHandler;
    $this->configFactory = $configFactory;
  }

  /**
   * @param string $moduleName
   *
   * @return array
   */
  public function checkModule(string $moduleName): array {
    $missingConfiguration = [];
    if (strpos($moduleName, 'degov') === FALSE) {
      return $missingConfiguration;
    }
    $files = file_scan_directory(drupal_get_path('module', $moduleName) . '/config/install', '/[a-z0-9_]*\.yml/');
    foreach ($files as $file) {
      $fileName = $file->filename;
      $configName = str_replace('.yml', '', $fileName);
      if (empty($this->configFactory->get($configName)->getRawData())) {
        $missingConfiguration[$moduleName][] = $configName;
      }
    }

    return $missingConfiguration;
  }

  /**
   * @param array $messages
   *
   * @return string
   */
  public function buildMessage(array $messages): string {
    $messageString = '';
    foreach ($messages as $message) {
      $messageString .= $message . ' ';
    }
    return $messageString;
  }

  /**
   * @return array
   */
  public function checkIntegrity(): array {
    $messages = [];
    $modules = $this->moduleHandler->getModuleList();

    foreach ($modules as $module) {
      $messages[] = $this->checkModule($module->getName());
    }

    return $messages;
  }

}
