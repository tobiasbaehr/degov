<?php

namespace Drupal\degov_common\Entity;

use Drupal\Core\Config\ConfigFactory;


class ConfigAdder {

  /**
   * @var \Drupal\Core\Config\ConfigFactory
   */
  private $configFactory;

  public function __construct(ConfigFactory $configFactory) {
    $this->configFactory = $configFactory;
  }

  public function addListItemFromConfiguration(string $configName, string $configPath, string $value) {
    $config = $this->configFactory
      ->getEditable($configName);
    $newValue = array_flip($config->get($configPath));
    $newValue[$value] = -1;
    $newValue = array_keys($newValue);
    $config->set($configPath, $newValue);
    $config->save(TRUE);
  }

}
