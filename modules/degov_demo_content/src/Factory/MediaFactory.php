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
   * Generates a set of media entities.
   */
  public function generateContent() {
    $media_to_generate = $this->loadDefinitions('media.yml');
    $fixtures_path = $this->moduleHandler->getModule('degov_demo_content')
        ->getPath() . '/fixtures';

    // Save files first.
    $file_ids = [];
    foreach ($media_to_generate as $media_item_key => $media_item) {
      $file_data = file_get_contents($fixtures_path . '/' . $media_item['file']);
      if (($saved_file = file_save_data($file_data, DEGOV_DEMO_CONTENT_FILES_SAVE_PATH . '/' . $media_item['file'], FILE_EXISTS_REPLACE)) !== FALSE) {
        $file_ids[$media_item_key] = $saved_file->id();
      }
    }

    // Create the Media entities.
    $saved_entities = [];
    foreach ($media_to_generate as $media_item_key => $media_item) {
      if (isset($file_ids[$media_item_key])) {
        $fields = [
          'bundle'      => $media_item['bundle'],
          'name'        => $media_item['name'],
          'field_title' => $media_item['name'],
          'status'      => !empty($media_item['status']) ? $media_item['status'] : TRUE,
          'field_tags'  => [
            ['target_id' => $this->getDemoContentTagId()],
          ],
        ];

        switch ($media_item['bundle']) {
          case 'image':
            $fields['image'] = [
              'target_id' => $file_ids[$media_item_key],
              'alt'       => $media_item['name'],
              'title'     => $media_item['name'],
            ];
            $fields['field_image_caption'] = $media_item['caption'] ?? '';
            $fields['field_royalty_free'] = $media_item['royalty_free'] ?? false;
            if(empty($media_item['royalty_free'])) {
              $fields['field_copyright'] = [
                'target_id' => $this->getDemoContentCopyrightId(),
              ];
            }
            break;
          case 'video_upload':
            $fields['field_video_upload_mp4'] = [
              'target_id' => $file_ids[$media_item_key],
            ];
            break;
          case 'document':
            $fields['field_document'] = [
              'target_id' => $file_ids[$media_item_key],
            ];
            break;
          case 'audio':
            $fields['field_audio_mp3'] = [
              'target_id' => $file_ids[$media_item_key],
            ];
            break;
        }

        $new_media = Media::create($fields);
        $new_media->save();
        $saved_entities[$media_item_key] = $new_media;
      }
    }

    // Create references between Media entities.
    foreach ($media_to_generate as $media_item_key => $media_item) {
      if (!empty($saved_entities[$media_item_key])) {
        $saved_entity = $saved_entities[$media_item_key];
        switch ($media_item['bundle']) {
          case 'video_upload':
            if (!empty($media_item['preview']['image'])) {
              $saved_entity->set('field_video_upload_preview', [
                'target_id' => isset($saved_entities[$media_item['preview']['image']]) ? $saved_entities[$media_item['preview']['image']]->id() : NULL,
              ]);
              $saved_entity->save();
            }
            break;
          case 'audio':
            if (!empty($media_item['preview']['image'])) {
              $saved_entity->set('field_audio_preview', [
                'target_id' => isset($saved_entities[$media_item['preview']['image']]) ? $saved_entities[$media_item['preview']['image']]->id() : NULL,
              ]);
              $saved_entity->save();
            }
            break;
        }
      }
    }
  }
}
