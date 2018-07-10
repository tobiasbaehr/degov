<?php

namespace Drupal\degov_common\Entity;


use Drupal\Core\Config\ConfigFactory;

class ConfigRemover {

  /**
   * @var \Drupal\Core\Config\ConfigFactory
   */
  private $configFactory;

  /**
   * WorkflowHandler constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactory $configFactory
   */
  public function __construct(ConfigFactory $configFactory)
  {
    $this->configFactory = $configFactory;
  }


  /**
   * removes a special value from configuration
   * @param string $configName
   * @param string $configPath
   * @param string $key
   */
 public function removeValueFromConfiguration(string $configName, string $configPath, string $key):void {
    $config = \Drupal::configFactory()
      ->getEditable($configName);
    $value = $config->get($configPath);
    if (array_key_exists($key, $value)) {
      unset($value[$key]);
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
