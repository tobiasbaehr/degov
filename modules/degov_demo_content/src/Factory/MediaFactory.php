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
  private $fileIds = [];

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
        $this->fileIds[$media_item_key] = $saved_file->id();
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
      if (isset($this->fileIds[$media_item_key])) {
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
              'target_id' => $this->fileIds[$media_item_key],
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
              'target_id' => $this->fileIds[$media_item_key],
            ];
            break;
          case 'document':
            $fields['field_document'] = [
              'target_id' => $this->fileIds[$media_item_key],
            ];
            break;
          case 'audio':
            $fields['field_audio_mp3'] = [
              'target_id' => $this->fileIds[$media_item_key],
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
}
