<?php

namespace Drupal\degov_demo_content\Generator;

use Drupal\Core\Entity\EntityFieldManager;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\degov_demo_content\MediaBundle;
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

  public function __construct(ModuleHandler $moduleHandler, EntityTypeManager $entityTypeManager, MediaBundle $mediaBundle, WktGenerator $wktGenerator) {
    parent::__construct($moduleHandler, $entityTypeManager, $mediaBundle);
    $this->wktGenerator = $wktGenerator;
  }

  /**
   * Generates a set of media entities.
   */
  public function generateContent(): void {
    $media_to_generate = $this->loadDefinitions('media.yml');

    $this->saveFiles($media_to_generate);
    $this->saveEntities($media_to_generate, FALSE);
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
   * Saves the files listed in the definitions as File entities.
   */
  private function saveFiles($media_to_generate): void {
    $fixtures_path = $this->moduleHandler->getModule('degov_demo_content')
      ->getPath() . '/fixtures';

    foreach ($media_to_generate as $media_item_key => $media_item) {
      if (isset($media_item['file'])) {
        $file_data = file_get_contents($fixtures_path . '/' . $media_item['file']);
        if (($saved_file = file_save_data($file_data, DEGOV_DEMO_CONTENT_FILES_SAVE_PATH . '/' . $media_item['file'], FILE_EXISTS_REPLACE)) !== FALSE) {
          $this->files[$media_item_key] = $saved_file;
        }
      }
    }
  }

  /**
   * Saves the defined Media entities.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  private function saveEntities($media_to_generate, $fullSave = TRUE): void {
    // Create the Media entities.
    foreach ($media_to_generate as $media_item_key => $media_item) {
      $this->prepareValues($media_item, $fullSave);
      $fields = [];

      foreach ($media_item as $media_item_field_key => $media_item_field_value) {
        if ($media_item_field_key === 'file') {
          switch ($media_item['bundle']) {
            case 'image':
              $fields['image'] = [
                'target_id' => $this->files[$media_item_key]->id(),
                'alt'       => $media_item['name'],
                'title'     => $media_item['name'],
              ];
              break;

            case 'document':
              $fields['field_document'] = [
                'target_id' => $this->files[$media_item_key]->id(),
              ];
              break;

            case 'audio':
              $fields['field_audio_mp3'] = [
                'target_id' => $this->files[$media_item_key]->id(),
              ];
              break;

            case 'video_upload':
              $fields['field_video_upload_mp4'] = [
                'target_id' => $this->files[$media_item_key]->id(),
              ];
              break;

            case 'facts':
              $fields['field_facts_image'] = [
                'target_id' => $this->files[$media_item_key]->id(),
                'alt'       => $media_item['name'],
                'title'     => $media_item['name'],
              ];
              break;
          }
          continue;
        }

        if ($media_item_field_key === 'field_address_address') {
          $fields['field_address_address'] = [
            $media_item['field_address_address'] ?? [],
          ];
          continue;
        }

        if ($media_item_field_key === 'field_address_location') {
          if (!empty($media_item['field_address_location'])) {
            $fields['field_address_location'] = $this->wktGenerator->wktBuildPoint($media_item['field_address_location']);
            continue;
          }
        }

        $fields[$media_item_field_key] = $media_item_field_value;
      }

      if ($media_item['bundle'] === 'image' && empty($media_item['field_royalty_free'])) {
        $fields['field_copyright'] = [
          'target_id' => $this->getDemoContentCopyrightId(),
        ];
      }

      $fields['field_title'] = $media_item['name'];
      $fields['status'] = $media_item['status'] ?? TRUE;

      if ($this->mediaBundle->bundleHasField('field_tags', $media_item['bundle'])) {
        $fields['field_tags'] = [
          ['target_id' => $this->getDemoContentTagId()],
        ];
      }

      if(empty($this->savedEntities[$media_item_key])) {
        $new_media = Media::create($fields);
        $new_media->save();
        $this->savedEntities[$media_item_key] = $new_media;
      } else {
        foreach($fields as $field => $value) {
          $this->savedEntities[$media_item_key]->set($field, $value);
        }
        $this->savedEntities[$media_item_key]->save();
      }
    }
  }

  /**
   * Resolve Media entity references, for example in preview image fields
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
