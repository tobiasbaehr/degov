<?php

namespace Drupal\degov_common\Entity;


use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Logger\LoggerChannelInterface;

class ConfigRemover {

  /**
   * @var \Drupal\Core\Config\ConfigFactory
   */
  private $configFactory;

  /**
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  private $logger;

  /**
   * WorkflowHandler constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactory $configFactory
   * @param \Drupal\Core\Logger\LoggerChannelInterface $logger
   */
  public function __construct(ConfigFactory $configFactory, LoggerChannelInterface $logger) {
    $this->configFactory = $configFactory;
    $this->logger = $logger;
  }

  /**
   * removes a special value from configuration
   * @param string $configName
   * @param string $configPath
   * @param string $key
   */
 public function removeValueFromConfiguration(string $configName, string $configPath, string $key):void {
   $this->logger->info("Opening configuration \"$configName.$configPath\"");
    $config = \Drupal::configFactory()
      ->getEditable($configName);
    $value = $config->get($configPath);
    if ($value && array_key_exists($key, $value)) {
      $this->logger->info("Removed key \"$key\" of value \"$value\" from configuration \"$configName.$configPath\"");
      unset($value[$key]);
    }
    else {
      $this->logger->info("Skipped configuration \"$configName.$configPath\"");
    }
    if ($value) {
      $config->set($configPath, $value);
    }
    else {
      $config->set($configPath, NULL);
    }
    $config->save(TRUE);
  }

}
