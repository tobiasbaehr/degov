<?php

namespace Drupal\degov_media_file_links\Service;

use Drupal\media\Entity\Media;
use Drupal\degov_media_file_links\Service\MediaFileFieldMapper;

/**
 * Class MediaFileLinkResolver.
 *
 * Accepts a Media entity ID and returns the primary file in the entity.
 *
 * @package Drupal\degov_media_file_links\Service
 */
class MediaFileLinkResolver {

  private $fileFieldMapper;

  public function __construct(MediaFileFieldMapper $fileFieldMapper) {
    $this->fileFieldMapper = $fileFieldMapper;
  }

  public function getFileUrlString(int $mediaId): string {
    $media = Media::load($mediaId);
    $mediaBundle = $media->bundle();
    error_log($mediaBundle);
    error_log($this->fileFieldMapper->getFileFieldForBundle($mediaBundle));
    $fileFieldName = $this->fileFieldMapper->getFileFieldForBundle($mediaBundle);
    $value = $media->get($fileFieldName);
  }
}
