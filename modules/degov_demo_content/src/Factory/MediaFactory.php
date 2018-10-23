<?php

namespace Drupal\degov_demo_content\Factory;

use Drupal\media\Entity\Media;

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
   * Generates a set of media entities.
   */
  public function generateContent() {
    $media_to_generate = $this->loadDefinitions('media.yml');

    $this->saveFiles($media_to_generate);
    $this->saveEntities($media_to_generate);
    $this->applyImageCrops($media_to_generate);
    $this->saveEntityReferences($media_to_generate);
  }

  public function resetContent() {
    $this->deleteContent();
    $this->generateContent();
  }

  /**
   * @param $media_to_generate
   *
   * @return array
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
   * @param $media_to_generate
   *
   * @return array
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  private function saveEntities($media_to_generate): void {
    // Create the Media entities.
    foreach ($media_to_generate as $media_item_key => $media_item) {
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

        switch ($media_item['bundle']) {
          case 'image':
            $fields['image'] = [
              'target_id' => $this->files[$media_item_key]->id(),
              'alt'       => $media_item['name'],
              'title'     => $media_item['name'],
            ];
            $fields['field_image_caption'] = $media_item['caption'] ?? '';
            $fields['field_royalty_free'] = $media_item['royalty_free'] ?? FALSE;
            if (empty($media_item['royalty_free'])) {
              $fields['field_copyright'] = [
                'target_id' => $this->getDemoContentCopyrightId(),
              ];
            }
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
        }

        $new_media = Media::create($fields);
        $new_media->save();
        $this->savedEntities[$media_item_key] = $new_media;
      }
    }
  }

  /**
   * @param $media_to_generate
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
        }
      }
    }
  }

  /**
   * @param $media_to_generate
   */
  private function applyImageCrops($media_to_generate): void {
    // Get all crop types
    $crop_types = \Drupal::entityTypeManager()
      ->getStorage('crop_type')
      ->loadMultiple();
    // for each file and every crop type store a new crop entity
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
   * @param $crop_type
   * @param $image_dimensions
   *
   * @return array
   */
  private function calculateCropDimensions($crop_type, $image_dimensions): array {
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
