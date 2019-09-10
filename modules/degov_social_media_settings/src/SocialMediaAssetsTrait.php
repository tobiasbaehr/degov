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
    // TODO refactor this.
    $filePath = implode('/', $path);
    $content = file_get_contents($filePath);
    $options = ['allowed_classes' => TRUE];
    return \unserialize($content, $options);
  }
}
