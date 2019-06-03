<?php

namespace Drupal\degov_demo_content\Generator;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\degov_demo_content\MediaFileHandler;
use Drupal\file\Entity\File;
use Drupal\geofield\WktGenerator;
use Drupal\media\Entity\Media;

/**
 * Class MediaGenerator.
 *
 * Generates Media entities from a YAML definition file.
 *
 * @package Drupal\degov_demo_content\Factory
 */
class MediaGenerator extends ContentGenerator implements GeneratorInterface {

  /**
   * The entity type we are working with.
   *
   * @var string
   */
  protected $entityType = 'media';

  /**
   * The ids of the Files we have saved.
   *
   * @var array
   */
  private $files = [];

  /**
   * The ids of the Media entities we have saved.
   *
   * @var array
   */
  private $savedEntities = [];

  /**
   * The Geofield WktGenerator.
   *
   * @var \Drupal\geofield\WktGenerator
   */
  protected $wktGenerator;

  /**
   * @var MediaFileHandler
   */
  private $mediaFileHandler;

  /**
   * Constructs a new ContentGenerator instance.
   *
   * @param \Drupal\Core\Extension\ModuleHandler $moduleHandler
   *   The ModuleHandler.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The EntityTypeManager.
   * @param \Drupal\geofield\WktGenerator $wktGenerator
   *   The Geofield WktGenerator.
   */
  public function __construct(ModuleHandler $moduleHandler, EntityTypeManagerInterface $entityTypeManager, WktGenerator $wktGenerator, MediaFileHandler $mediaFileHandler) {
    parent::__construct($moduleHandler, $entityTypeManager);
    $this->wktGenerator = $wktGenerator;
    $this->mediaFileHandler = $mediaFileHandler;
  }

  /**
   * Generates a set of media entities.
   */
  public function generateContent(): void {
    $media_to_generate = $this->loadDefinitions('media.yml');

    $fixtures_path = $this->moduleHandler->getModule('degov_demo_content')->getPath() . '/fixtures';
    $this->mediaFileHandler->saveFiles($media_to_generate, $fixtures_path);
    $this->saveEntities($media_to_generate);
    $this->saveEntityReferences($media_to_generate);
  }

  /**
   * Deletes and regenerates all demo Media.
   */
  public function resetContent(): void {
    $this->deleteContent();
    $this->generateContent();
  }

  /**
   * Saves the defined Media entities.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  private function saveEntities($media_to_generate): void {
    $fields = null;
    // Create the Media entities.
    foreach ($media_to_generate as $media_item_key => $media_item) {
      $this->prepareValues($media_item);

      $fields = $this->mediaFileHandler->mapFileFields($media_item, $media_item_key);

      $fields['field_title'] = $media_item['name'];
      $fields['status'] = $media_item['status'] ?? TRUE;
      $fields['field_tags'] = [
        ['target_id' => $this->getDemoContentTagId()],
      ];
      if (empty($media_item['field_royalty_free'])) {
        $fields['field_copyright'] = [
          'target_id' => $this->getDemoContentCopyrightId(),
        ];
      }

      if ($media_item_key === 'field_address_address') {
        $fields['field_address_address'] = [
          $media_item['field_address_address'] ?? [],
        ];
        continue;
      }

      if ($media_item_key === 'field_address_location') {
        if (!empty($media_item['field_address_location'])) {
          $fields['field_address_location'] = $this->wktGenerator->wktBuildPoint($media_item['field_address_location']);
          continue;
        }
      }

      $new_media = Media::create($fields);
      if (!empty($new_media)) {
        $new_media->save();
        $this->savedEntities[$media_item_key] = $new_media;
      }
      else {
        \Drupal::logger('degov_demo_content')
          ->error('Failed to create Media entity with these fields: ' . print_r($fields, 1));
      }
    }
  }

  /**
   * Store references between Media entities, e.g. preview images.
   */
  private function saveEntityReferences($media_to_generate): void {
    // Create references between Media entities.
    foreach ($media_to_generate as $media_item_key => $media_item) {
      if (!empty($this->savedEntities[$media_item_key])) {
        $saved_entity = $this->savedEntities[$media_item_key];
        if (($media_item['bundle'] === 'gallery') && !empty($media_item['field_gallery_images'])) {
          $image_target_ids = [];
          foreach ($media_item['field_gallery_images'] as $image_key) {
            if (isset($this->savedEntities[$image_key])) {
              $image_target_ids[] = [
                'target_id' => $this->savedEntities[$image_key]->id(),
              ];
            }
          }
          $saved_entity->set('field_gallery_images', $image_target_ids);
          $saved_entity->save();

        }
      }
    }
  }

  /**
   * Deletes the generated entities.
   */
  public function deleteContent(): void {
    $query = \Drupal::entityQuery('file');
    $query->condition('uri', 'public://degov_demo_content/%', 'LIKE');
    $query_results = $query->execute();

    if (!empty($query_results)) {
      $file_entities = File::loadMultiple($query_results);

      foreach ($file_entities as $file_entity) {
        $file_entity->delete();
      }
    }
    parent::deleteContent();
  }

}
