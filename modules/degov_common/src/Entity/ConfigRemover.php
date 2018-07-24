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

  public function __construct(ConfigFactory $configFactory, LoggerChannelInterface $logger) {
    $this->configFactory = $configFactory;
    $this->logger = $logger;
  }

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
    $value = array_flip($config->get($configPath));
    $value[$key] = -1;
    $value = array_keys($value);
    $config->set($configPath, $value);
    $config->save(TRUE);
  }
}
