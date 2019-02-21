<?php

namespace Drupal\degov_media_file_links\Service;

use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use Drupal\node\NodeInterface;

/**
 * Class MediaFileLinkSuggester.
 *
 * Accepts a (partial) search string and finds matching Media by title and
 * filename.
 *
 * @package Drupal\degov_media_file_links\Service
 */
class MediaFileSuggester {

  private $fileFieldMapper;

  /**
   * MediaFileSuggester constructor.
   *
   * @param \Drupal\degov_media_file_links\Service\MediaFileFieldMapper $fileFieldMapper
   */
  public function __construct(MediaFileFieldMapper $fileFieldMapper) {
    $this->fileFieldMapper = $fileFieldMapper;
  }

  /**
   * Runs searches on Media titles and filenames, returns the merged results.
   *
   * @param string $search
   *
   * @return array
   */
  public function findBySearchString(string $search): array {
    if (\strlen($search) < 3) {
      return [];
    }
    return array_merge($this->findBySearchInTitle($search), $this->findBySearchInFilename($search));
  }

  /**
   * Runs a plain search on Media titles.
   *
   * @param string $search
   *
   * @return array
   */
  private function findBySearchInTitle(string $search): array {
    $mediaQuery = \Drupal::entityQuery('media')
      ->condition('status', NodeInterface::PUBLISHED)
      ->condition('bundle', $this->fileFieldMapper->getEnabledBundles(), 'IN')
      ->condition('name', $search, 'CONTAINS');
    $mediaIds = $mediaQuery->execute();
    return $this->prepareResultsFromIds($mediaIds);
  }

  /**
   * Performs a search on file names and resolves the corresponding Media.
   *
   * @param string $search
   *
   * @return array
   */
  private function findBySearchInFilename(string $search): array {
    $filesQuery = \Drupal::entityQuery('file')
      ->condition('status', NodeInterface::PUBLISHED)
      ->condition('filename', $search, 'CONTAINS');
    $fileIds = $filesQuery->execute();

    if (empty($fileIds)) {
      return [];
    }

    $mediaQuery = \Drupal::entityQuery('media');
    $fieldValueCombinationsGroup = $mediaQuery->orConditionGroup();
    foreach ($this->fileFieldMapper->getBundleFileFieldMappings() as $bundle => $fileField) {
      $fieldValueCombinationsGroup->condition($fileField, $fileIds, 'IN');
    }

    $mediaQuery->condition('status', NodeInterface::PUBLISHED)
      ->condition('bundle', $this->fileFieldMapper->getEnabledBundles(), 'IN')
      ->condition($fieldValueCombinationsGroup);
    $mediaIds = $mediaQuery->execute();
    return $this->prepareResultsFromIds($mediaIds);
  }

  /**
   * Turns an array of entity ids into an array of search results.
   *
   * @param array $ids
   *
   * @return array
   */
  private function prepareResultsFromIds(array $ids): array {
    $preparedResults = [];
    if (\count($ids) > 0) {
      $entities = Media::loadMultiple($ids);
      foreach ($entities as $entity) {
        $nameValue = $entity->get('name')->getValue();
        $preparedResults[] = [
          'id'       => $entity->id(),
          'title'    => $nameValue[0]['value'] ?? '',
          'bundle'   => $entity->bundle(),
          'mimetype' => $this->getFileTypeForEntity($entity),
        ];
      }
    }
    return $preparedResults;
  }

  /**
   * Accepts a Media entity and returns the mime type of the primary file.
   *
   * @param \Drupal\media\Entity\Media $media
   *   The Media entity to retrieve the mime type from.
   *
   * @return string
   *   The mime type of the entity's primary file.
   */
  private function getFileTypeForEntity(Media $media): string {
    $fileField = $this->fileFieldMapper->getFileFieldForBundle($media->bundle());
    $value = $media->get($fileField)->getValue();
    if (isset($value[0]['target_id'])) {
      $file = File::load($value[0]['target_id']);
      return $file->getMimeType();
    }
    return '';
  }

}
