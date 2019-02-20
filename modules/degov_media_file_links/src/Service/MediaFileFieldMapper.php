<?php

namespace Drupal\degov_media_file_links\Service;

/**
 * Class MediaFileFieldMapper.
 *
 * Maps Media bundles to their primary file fields.
 *
 * @package Drupal\degov_media_file_links\Service
 */
class MediaFileFieldMapper {

  /**
   * Accepts a bundle name and returns the main file field for this bundle.
   *
   * @param string $bundle
   *   The machine name of the bundle.
   *
   * @return string
   *   The machine name of the main file field associated with the bundle.
   */
  public function getFileFieldForBundle(string $bundle): string {
    $fileFieldMappings = $this->getBundleFileFieldMappings();
    if (isset($fileFieldMappings[$bundle])) {
      return $fileFieldMappings;
    }
    return FALSE;
  }

  /**
   * Get a list of bundles and their main file fields.
   *
   * Moved this into a separate function because we might eventually load the
   * pairings from config instead of providing a static array.
   *
   * @return array
   *   The array of bundle / field pairs.
   */
  private function getBundleFileFieldMappings(): array {
    return [
      'document' => 'field_document',
    ];
  }

}
