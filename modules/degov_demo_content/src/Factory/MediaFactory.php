<?php

namespace Drupal\degov_demo_content\Factory;

use Drupal\media\Entity\Media;

/**
 * Class MediaFactory.
 *
 * Generates Media entities from a YAML definition file.
 *
 * @package Drupal\degov_demo_content\Factory
 */
class MediaFactory extends ContentFactory {

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
   * Constructs a new ContentFactory instance.
   *
   * @param WktGenerator $wktGenerator
   */
  public function __construct($wktGenerator) {
    parent::__construct();
    $this->wktGenerator = $wktGenerator;
  }

  /**
   * Generates a set of media entities.
   */
  public function generateContent() {
    $media_to_generate = $this->loadDefinitions('media.yml');

    $this->saveFiles($media_to_generate);
    $this->saveEntities($media_to_generate);
    $this->saveEntityReferences($media_to_generate);
    $this->applyImageCrops();
  }

  /**
   * Deletes and regenerates all demo Media.
   */
  public function resetContent() {
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
      $file_data = file_get_contents($fixtures_path . '/' . $media_item['file']);
      if (($saved_file = file_save_data($file_data, DEGOV_DEMO_CONTENT_FILES_SAVE_PATH . '/' . $media_item['file'], FILE_EXISTS_REPLACE)) !== FALSE) {
        $this->files[$media_item_key] = $saved_file;
      }
    }
  }

  /**
   * Saves the defined Media entities.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  private function saveEntities($media_to_generate): void {
    // Create the Media entities.
    foreach ($media_to_generate as $media_item_key => $media_item) {
      $this->prepareValues($media_item);

      if (isset($this->files[$media_item_key])) {
        $fields = [
          'bundle'      => $media_item['bundle'],
          'name'        => $media_item['name'],
          'field_title' => $media_item['name'],
          'status'      => $media_item['status'] ?? TRUE,
          'uid'         => $media_item['user_id'] ?? 1,
          'field_tags'  => [
            ['target_id' => $this->getDemoContentTagId()],
          ],
        ];

        if(isset($media_item['embed_code'])) {
          $fields['embed_code'] = $media_item['embed_code'];
        }

        if(isset($media_item['field_media_publish_date'])) {
          $fields['field_media_publish_date'] = $media_item['field_media_publish_date'];
        }

        if (empty($media_item['field_royalty_free'])) {
          $fields['field_copyright'] = [
            'target_id' => $this->getDemoContentCopyrightId(),
          ];
        }

        switch ($media_item['bundle']) {
          case 'image':
            $fields['image'] = [
              'target_id' => $this->files[$media_item_key]->id(),
              'alt'       => $media_item['name'],
              'title'     => $media_item['name'],
            ];
            $fields['field_image_caption'] = $media_item['field_image_caption'] ?? '';
            $fields['field_royalty_free'] = $media_item['field_royalty_free'] ?? FALSE;
            break;

          case 'video_upload':
            $fields['field_video_upload_mp4'] = [
              'target_id' => $this->files[$media_item_key]->id(),
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

          case 'address':
            $fields['field_address_address'] = [
              $media_item['field_address_address'] ?? [],
            ];
            if(!empty($media_item['field_address_location'])) {
              $fields['field_address_location'] = $this->wktGenerator->wktBuildPoint($media_item['field_address_location']);
            }
            $fields['field_address_title'] = $media_item['field_address_title'] ?? '';
            $fields['field_address_phone'] = $media_item['field_address_phone'] ?? '';
            $fields['field_address_fax'] = $media_item['field_address_fax'] ?? '';
            $fields['field_address_email'] = $media_item['field_address_email'] ?? '';
            break;

          case 'citation':
            $fields['field_citation_date'] = $media_item['field_citation_date'];
            $fields['field_citation_text'] = $media_item['field_citation_text'];
            $fields['field_citation_title'] = $media_item['field_citation_title'];
            break;

          case 'person':
            $fields['field_person_info'] = $media_item['field_person_info'];
            break;

          case 'contact':
            $fields['field_contact_email'] = $media_item['field_contact_email'];
            $fields['field_contact_fax'] = $media_item['field_contact_fax'];
            $fields['field_contact_title'] = $media_item['field_contact_title'];
            $fields['field_contact_position'] = $media_item['field_contact_position'];
            $fields['field_contact_tel'] = $media_item['field_contact_tel'];
            break;

          case 'gallery':
            $fields['field_description'] = $media_item['field_description'];
            $fields['field_gallery_title'] = $media_item['field_gallery_title'];
            $fields['field_subtitle'] = $media_item['field_subtitle'];
            break;

          case 'some_embed':
            $fields['field_some_embed_code'] = $media_item['field_some_embed_code'];
            $fields['field_social_media_source'] = $media_item['field_social_media_source'];
            break;

          case 'video':
            $fields['field_description'] = $media_item['field_description'];
            $fields['field_video_caption'] = $media_item['field_video_caption'];
            $fields['field_social_media_source'] = $media_item['field_social_media_source'];
            $fields['field_media_transcription'] = $media_item['field_media_transcription'];
            $fields['field_subtitle'] = $media_item['field_subtitle'];
            $fields['field_media_publish_date'] = $media_item['field_media_publish_date'];
            $fields['field_media_duration'] = $media_item['field_media_duration'];
            $fields['field_media_video_embed_field'] = $media_item['field_media_video_embed_field'];
            break;

        }

        $new_media = Media::create($fields);
        $new_media->save();
        $this->savedEntities[$media_item_key] = $new_media;
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
        switch ($media_item['bundle']) {
          case 'video_upload':
            if (!empty($media_item['preview']['image'])) {
              $saved_entity->set('field_video_upload_preview', [
                'target_id' => isset($this->savedEntities[$media_item['preview']['image']]) ? $this->savedEntities[$media_item['preview']['image']]->id() : NULL,
              ]);
              $saved_entity->save();
            }
            break;

          case 'audio':
            if (!empty($media_item['preview']['image'])) {
              $saved_entity->set('field_audio_preview', [
                'target_id' => isset($this->savedEntities[$media_item['preview']['image']]) ? $this->savedEntities[$media_item['preview']['image']]->id() : NULL,
              ]);
              $saved_entity->save();
            }
            break;

          case 'citation':
            if (!empty($media_item['field_citation_image'])) {
              $saved_entity->set('field_citation_image', [
                'target_id' => isset($this->savedEntities[$media_item['field_citation_image']]) ? $this->savedEntities[$media_item['field_citation_image']]->id() : NULL,
              ]);
              $saved_entity->save();
            }
            break;

          case 'person':
            if (!empty($media_item['field_person_image'])) {
              $saved_entity->set('field_person_image', [
                'target_id' => isset($this->savedEntities[$media_item['field_person_image']]) ? $this->savedEntities[$media_item['field_person_image']]->id() : NULL,
              ]);
              $saved_entity->save();
            }
            break;

          case 'contact':
            if (!empty($media_item['field_contact_image'])) {
              $saved_entity->set('field_contact_image', [
                'target_id' => isset($this->savedEntities[$media_item['field_contact_image']]) ? $this->savedEntities[$media_item['field_contact_image']]->id() : NULL,
              ]);
              $saved_entity->save();
            }
            break;

          case 'gallery':
            if (!empty($media_item['field_gallery_images'])) {
              $image_target_ids = [];
              foreach($media_item['field_gallery_images'] as $image_key) {
                if(isset($this->savedEntities[$image_key])) {
                  $image_target_ids[] = [
                    'target_id' => $this->savedEntities[$image_key]->id(),
                  ];
                }
              }
              $saved_entity->set('field_gallery_images', $image_target_ids);
              $saved_entity->save();
            }
            break;

          case 'video':
            if (!empty($media_item['field_video_preview'])) {
              $saved_entity->set('field_video_preview', [
                'target_id' => isset($this->savedEntities[$media_item['field_video_preview']]) ? $this->savedEntities[$media_item['field_video_preview']]->id() : NULL,
              ]);
              $saved_entity->save();
            }
            break;
        }
      }
    }
  }

  /**
   * Apply all defined crop types to all image files in our definitions.
   */
  private function applyImageCrops(): void {
    $crop_types = \Drupal::entityTypeManager()
      ->getStorage('crop_type')
      ->loadMultiple();
    foreach ($this->files as $file) {
      if (preg_match("/^image\//", $file->getMimeType())) {
        foreach ($crop_types as $crop_type) {
          $image_dimensions = getimagesize(\Drupal::service('file_system')
            ->realpath($file->getFileUri()));

          $crop = \Drupal::entityTypeManager()
            ->getStorage('crop')
            ->loadByProperties([
              'type' => $crop_type->id(),
              'uri'  => $file->getFileUri(),
            ]);

          $measurements = $this->calculateCropDimensions($crop_type, $image_dimensions);

          if (empty($crop)) {
            $crop_values = [
              'type'        => $crop_type->id(),
              'entity_id'   => $file->id(),
              'entity_type' => 'file',
              'uri'         => $file->getFileUri(),
              'x'           => $image_dimensions[0] / 2,
              'y'           => $image_dimensions[1] / 2,
            ];
            $crop_values += $measurements;
            $crop = \Drupal::entityTypeManager()
              ->getStorage('crop')
              ->create($crop_values);
          }
          else {
            $crop = reset($crop);
            $crop->set('x', $image_dimensions[0] / 2);
            $crop->set('y', $image_dimensions[1] / 2);
            $crop->set('width', $measurements['width']);
            $crop->set('height', $measurements['height']);
          }
          $crop->save();
        }
      }
    }
  }

  /**
   * Calculate the height and width of the crop frame to be applied.
   *
   * @param object $crop_type
   *   The Crop crop_type we want to apply.
   * @param array $image_dimensions
   *   The actual width and height of the image file.
   *
   * @return array
   *   The desired height and width of the image.
   */
  private function calculateCropDimensions($crop_type, array $image_dimensions): array {
    $aspect_ratio_fragments = explode(':', $crop_type->aspect_ratio);
    if (count($aspect_ratio_fragments) !== 2) {
      $aspect_ratio_fragments = [1, 1];
    }

    if ($image_dimensions[0] >= $image_dimensions[1]) {
      // Landscape orientation.
      $measurements = [
        'width'  => (($image_dimensions[1] / $aspect_ratio_fragments[1]) * $aspect_ratio_fragments[0]) * $this->calculateScaleFactor($image_dimensions, $aspect_ratio_fragments),
        'height' => $image_dimensions[1] * $this->calculateScaleFactor($image_dimensions, $aspect_ratio_fragments),
      ];
    }
    else {
      // Portrait orientation.
      $measurements = [
        'width'  => $image_dimensions[0] * $this->calculateScaleFactor($image_dimensions, $aspect_ratio_fragments),
        'height' => (($image_dimensions[0] / $aspect_ratio_fragments[0]) * $aspect_ratio_fragments[1]) * $this->calculateScaleFactor($image_dimensions, $aspect_ratio_fragments),
      ];
    }

    return $measurements;
  }

  /**
   * Calculates a scape factor for the crop frame.
   *
   * @param array $image_dimensions
   *   The dimensions of the image.
   * @param array $aspect_ratio_fragments
   *   The aspect ratio of the crop type.
   *
   * @return float|int
   *   The factor to scale the image by.
   */
  private function calculateScaleFactor(array $image_dimensions, array $aspect_ratio_fragments) {
    if ($image_dimensions[0] > $image_dimensions[1]) {
      $image_ratio = $image_dimensions[0] / $image_dimensions[1];
      $crop_ratio = $aspect_ratio_fragments[0] / $aspect_ratio_fragments[1];
    }
    else {
      $image_ratio = $image_dimensions[1] / $image_dimensions[0];
      $crop_ratio = $aspect_ratio_fragments[1] / $aspect_ratio_fragments[0];
    }
    if ($crop_ratio > $image_ratio) {
      return $image_ratio / $crop_ratio;
    }
    return 1;
  }

}
