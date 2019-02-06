<?php

namespace Drupal\degov_config_integrity;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Class DegovModuleIntegrityChecker.
 *
 * @package Drupal\degov_config_integrity
 */
class DegovModuleIntegrityChecker {

  /**
   * The ModuleHandler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  private $moduleHandler;

  /**
   * The ConfigFactory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  private $configFactory;

  /**
   * DegovModuleIntegrityChecker constructor.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The ModuleHandler.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The ConfigFactory.
   */
  public function __construct(ModuleHandlerInterface $moduleHandler, ConfigFactoryInterface $configFactory) {
    $this->moduleHandler = $moduleHandler;
    $this->configFactory = $configFactory;
  }

  /**
   * Checks if the install-configs for a given module are in storage.
   *
   * @param string $moduleName
   *   The name of the module to check configs for.
   *
   * @return array
   *   The list of missing configs for this module.
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
   * Combines an array of strings into one message string.
   *
   * @param array $messages
   *   The array of strings to be combined into one message.
   *
   * @return string
   *   Values of $messages in one line with spaces.
   */
  public function buildMessage(array $messages): string {
    $messageString = '';
    foreach ($messages as $message) {
      $messageString .= $message . ' ';
    }
    return $messageString;
  }

  /**
   * Runs through all modules and checks their configurations.
   *
   * @return array
   *   The list of missing configs by module.
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
