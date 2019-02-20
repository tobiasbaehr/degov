<?php

namespace Drupal\degov_media_file_links\Service;

use Drupal\media\Entity\Media;

/**
 * Class MediaFileFieldMapper.
 *
 * Maps Media bundles to their primary file fields.
 *
 * @package Drupal\degov_media_file_links\Service
 */
class MediaFileFieldMapper {

  public function __construct() {
  }

  public function getFileFieldForBundle(string $bundle): string {
    $fileFieldMappings = $this->getBundleFileFieldMappings();
    if (isset($fileFieldMappings[$bundle])) {
      return $fileFieldMappings;
    }
    return FALSE;
  }

  private function getBundleFileFieldMappings(): array {
    return [
      'document' => 'field_document',
    ];
  }
}
