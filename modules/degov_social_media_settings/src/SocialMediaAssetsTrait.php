<?php

namespace Drupal\degov_social_media_settings;

/**
 * Trait SocialMediaAssetsTrait
 *
 * @package Drupal\degov_social_media_settings
 */
trait SocialMediaAssetsTrait {

  /**
   * Returns the content from storage file.
   *
   * @param string $moduleName
   *   The name of module.
   * @param string $fileName
   *   The name of file.
   *
   * @return mixed
   *   Unserialized data.
   */
  private static function getDataFromFile($moduleName, $fileName) {
    $path = [
      drupal_get_path('module', $moduleName),
      'assets',
      $fileName,
    ];
    $filePath = implode('/', $path);
    $content = file_get_contents($filePath);

    return unserialize($content);
  }

}
