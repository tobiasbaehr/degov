<?php

namespace Drupal\degov_media_file_links\Service;

use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;

/**
 * Class MediaFileLinkResolver.
 *
 * Accepts a Media entity ID and returns the primary file in the entity.
 *
 * @package Drupal\degov_media_file_links\Service
 */
class MediaFileLinkResolver {

  private $fileFieldMapper;

  /**
   * MediaFileLinkResolver constructor.
   *
   * @param \Drupal\degov_media_file_links\Service\MediaFileFieldMapper $fileFieldMapper
   */
  public function __construct(MediaFileFieldMapper $fileFieldMapper) {
    $this->fileFieldMapper = $fileFieldMapper;
  }

  /**
   * Accepts the id of a Media entity, returns the primary file URL.
   *
   * @param int $mediaId
   *
   * @return string
   */
  public function getFileUrlString(int $mediaId): string {
    $media = Media::load($mediaId);
    $mediaBundle = $media->bundle();
    $fileFieldName = $this->fileFieldMapper->getFileFieldForBundle($mediaBundle);
    $value = $media->get($fileFieldName)->getValue();
    if (isset($value[0]['target_id'])) {
      $file = File::load($value[0]['target_id']);
      $uri = $file->getFileUri();
      return Url::fromUri(file_create_url($uri))->toString();
    }
    return '';
  }

}
