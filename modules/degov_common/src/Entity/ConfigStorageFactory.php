<?php

namespace Drupal\degov_common\Entity;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Config\Config;
use Drupal\degov_common\Entity\Exception\StorageMissingConfigObjectException;

class ConfigStorageFactory {

  private $configFactory;

  public function __construct(ConfigFactory $configFactory) {
    $this->configFactory = $configFactory;
  }

  public function getEditable(string $configObjectName): Config {
    $config = $this->configFactory->getEditable($configObjectName);
    if (!$config->isNew()) {
      return $config;
    } else {
      throw new StorageMissingConfigObjectException('Expected config object named "' . $configObjectName . '" is missing in the storage. Accidentally removed config? Config import failed?');
    }
  }

}
