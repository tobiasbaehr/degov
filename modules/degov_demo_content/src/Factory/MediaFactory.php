<?php

namespace Drupal\degov_demo_content\Factory;

use Drupal\media\Entity\Media;

class MediaFactory extends ContentFactory {

  private $demo_files_save_path = 'public://degov_demo_content';

  /**
   * Constructs a new MediaFactory instance.
   */
  public function __construct() {
    parent::__construct();
    file_prepare_directory($this->demo_files_save_path, FILE_CREATE_DIRECTORY);
  }

  /**
   * Generates a set of media entities.
   */
  public function generateContent() {
    $media_to_generate = $this->loadDefinitions('media.yml');
    print_r($media_to_generate);
    $images_fixtures_path = $this->moduleHandler->getModule('degov_demo_content')
        ->getPath() . '/fixtures/images';

    foreach ($media_to_generate as $media_item) {
      $image_data = file_get_contents($images_fixtures_path . '/' . $media_item['file']);
      if (($saved_file = file_save_data($image_data, $this->demo_files_save_path . '/' . $media_item['file'], FILE_EXISTS_REPLACE)) !== FALSE) {
        $new_media = Media::create([
          'bundle' => $media_item['bundle'],
          'name'   => $media_item['name'],
          'status' => $media_item['status'],
          'image'  => [
            'target_id' => $saved_file->id(),
            'alt'       => $media_item['name'],
            'title'     => $media_item['name'],
          ],
        ]);
        $new_media->save();
      }
    }
  }
}