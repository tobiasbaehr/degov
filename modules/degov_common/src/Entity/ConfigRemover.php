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
   *
   * @param string $configName
   * @param string $configPath
   * @param string $key
   */
  public function removeValueFromConfiguration(string $configName, string $configPath, string $key): void {
    $config = \Drupal::configFactory()
      ->getEditable($configName);
    $value = $config->get($configPath);
    if ($value && array_key_exists($key, $value)) {
      unset($value[$key]);
      $config->set($configPath, $value);
      $config->save(TRUE);
    }
  }

  /**
   * removes a special value from configuration
   *
   * @param string $configName
   * @param string $configPath
   * @param string $key
   */
  public function removeListItemFromConfiguration(string $configName, string $configPath, string $key): void {
    $config = \Drupal::configFactory()
      ->getEditable($configName);
    $value = $config->get($configPath);
    if (!$value) {
      return;
    }
    $value = array_flip($value);
    if (array_key_exists($key, $value)) {
      unset($value[$key]);
      if ($value) {
        $value = array_keys($value);
        $config->set($configPath, $value);
        $config->save(TRUE);
      }
      else {
        //Remove element if it's empty
        $configParts = explode('.', $configPath);
        $newKey = array_pop($configParts);
        $newPath = implode('.', $configParts);
        $this->removeValueFromConfiguration($configName, $newPath, $newKey);
      }
    }
  }

  public function addListItemFromConfiguration($configName, $configPath, $key) {
    $config = \Drupal::configFactory()
      ->getEditable($configName);
    $value[$key] = -1;
    $value = array_keys($value);
    $config->set($configPath, $value);
    $config->save(TRUE);


  }
}
