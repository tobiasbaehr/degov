<?php

namespace Drupal\degov_common;

use Drupal\Core\Config\StorageInterface;

/**
 * Interface DegovConfigManager
 *
 * @package Drupal\degov_common
 */
interface DegovConfigManager {

  /**
   * Imports all the changes for the block configuration with batch.
   *
   * @param \Drupal\Core\Config\StorageInterface $sourceStorage
   */
  public function configImport(StorageInterface $sourceStorage) : void ;

}
