<?php

namespace Drupal\media_file_links\Service;

use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use Drupal\node\NodeInterface;

/**
 * Class MediaFileLinkSuggester.
 *
 * Accepts a (partial) search string and finds matching Media by title and
 * filename.
 *
 * @package Drupal\media_file_links\Service
 */
class MediaFileSuggester {

  private $fileFieldMapper;

  private $fileLinkResolver;

  /**
   * MediaFileSuggester constructor.
   *
   * @param \Drupal\media_file_links\Service\MediaFileFieldMapper $fileFieldMapper
   * @param \Drupal\media_file_links\Service\MediaFileLinkResolver $fileLinkResolver
   */
  public function __construct(MediaFileFieldMapper $fileFieldMapper, MediaFileLinkResolver $fileLinkResolver) {
    $this->fileFieldMapper = $fileFieldMapper;
    $this->fileLinkResolver = $fileLinkResolver;
  }

  /**
   * Runs searches on Media titles and filenames, returns the merged results.
   *
   * @param string $search
   * @param bool $returnJson
   *
   * @return array
   */
  public function findBySearchString(string $search, bool $returnJson = TRUE) {
    $results = array_merge($this->findBySearchInTitle($search), $this->findBySearchInFilename($search));
    $preparedResults = $this->prepareResults($results);

    if ($returnJson) {
      return json_encode($preparedResults);
    }

    return $preparedResults;
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
    if (\count($mediaIds) > 0) {
      return Media::loadMultiple($mediaIds);
    }
    return [];
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
    if (\count($mediaIds) > 0) {
      return Media::loadMultiple($mediaIds);
    }
    return [];
  }

  /**
   * Turns an array of search results into a json string.
   *
   * @param array $results
   *
   * @return string
   */
  private function prepareResults(array $results): array {
    $preparedResults = [];
    $mediaBundles = \Drupal::service('entity_type.bundle.info')
      ->getBundleInfo('media');
    if (\count($results) > 0) {
      foreach ($results as $entity) {
        $nameValue = $entity->get('name')->getValue();
        if(!empty($nameValue[0]['value'])) {
          $iconClass = $this->getIconClassForFile($this->fileLinkResolver->getFileNameString($entity->id()));
          $preparedResults[] = [
            'id'          => $entity->id(),
            'title'       => $nameValue[0]['value'],
            'bundle'      => $entity->bundle(),
            'bundleLabel' => $mediaBundles[$entity->bundle()]['label'] ?? $entity->bundle(),
            'mimetype'    => $this->getFileTypeForEntity($entity),
            'iconClass'   => $iconClass,
          ];
        }
      }
    }
    return $preparedResults;
  }

  private function getIconClassForFile(string $filename) {
    $iconClasses = [
      'fas fa-file-alt'        => ['doc', 'docx', 'odt'],
      'fas fa-file-excel'      => ['xls', 'xlsx', 'csv', 'ods'],
      'fas fa-file-powerpoint' => ['ppt', 'pptx', 'odp'],
      'fas fa-file-pdf'        => ['pdf'],
    ];

    $extension = pathinfo($filename, PATHINFO_EXTENSION);

    foreach ($iconClasses as $iconClass => $extensions) {
      if (in_array($extension, $extensions)) {
        return $iconClass;
      }
    }

    return '';
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
    if (!empty($fileField)) {
      $value = $media->get($fileField)->getValue();
      if (isset($value[0]['target_id'])) {
        $file = File::load($value[0]['target_id']);
        return $file->getMimeType();
      }
    }
    return '';
  }

}
