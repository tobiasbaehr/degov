<?php

namespace Drupal\media_file_links\Service;

use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\file\FileInterface;
use Drupal\media\Entity\Media;
use Drupal\media\MediaInterface;

/**
 * Class MediaFileLinkResolver.
 *
 * Accepts a Media entity ID and returns the primary file in the entity.
 *
 * @package Drupal\media_file_links\Service
 */
class MediaFileLinkResolver {

  /**
   * File field mapper.
   *
   * @var \Drupal\media_file_links\Service\MediaFileFieldMapper
   */
  private $fileFieldMapper;

  /**
   * MediaFileLinkResolver constructor.
   *
   * @param \Drupal\media_file_links\Service\MediaFileFieldMapper $fileFieldMapper
   *   File field mapper.
   */
  public function __construct(MediaFileFieldMapper $fileFieldMapper) {
    $this->fileFieldMapper = $fileFieldMapper;
  }

  /**
   * Accepts the id of a Media entity, returns the primary file URL.
   *
   * @param int $mediaId
   *   Media ID.
   *
   * @return string
   *   File url string.
   */
  public function getFileUrlString(int $mediaId): string {
    $file = $this->getFileForMedia($mediaId);
    if ($file instanceof FileInterface) {
      $uri = $file->getFileUri();
      return Url::fromUri(file_create_url($uri))->toString();
    }

    \Drupal::logger('media_file_links')->warning(
      t('Requested file for Media ID %id could not be found.', ['%id' => $mediaId])
    );
    return '';
  }

  /**
   * Get file name string.
   *
   * @param int $mediaId
   *   Media ID.
   *
   * @return string
   *   File name string.
   */
  public function getFileNameString(int $mediaId): string {
    $file = $this->getFileForMedia($mediaId);

    if ($file instanceof FileInterface) {
      return $file->getFilename();
    }

    \Drupal::logger('media_file_links')->warning(
      t('Requested file for Media ID %id could not be found.', ['%id' => $mediaId])
    );
    return '';
  }

  /**
   * Accepts a Media id and returns the primary file of the entity.
   *
   * @param int $mediaId
   *   Media ID.
   *
   * @return \Drupal\file\FileInterface|null
   *   File.
   */
  private function getFileForMedia(int $mediaId): ?FileInterface {
    $media = Media::load($mediaId);
    if ($media instanceof MediaInterface) {
      $mediaBundle = $media->bundle();
      $fileFieldName = $this->fileFieldMapper->getFileFieldForBundle($mediaBundle);
      if (!empty($fileFieldName)) {
        $value = $media->get($fileFieldName)->getValue();
        if (isset($value[0]['target_id'])) {
          return File::load($value[0]['target_id']);
        }
      }
    }
    return NULL;
  }

}
