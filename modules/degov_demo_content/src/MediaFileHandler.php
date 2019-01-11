<?php

namespace Drupal\degov_demo_content;

use Drupal\degov_common\Factory\FilesystemFactory;
use Drupal\degov_demo_content\FileAdapter;
use Drupal\file\Entity\File;
use Symfony\Component\Filesystem\Filesystem;


class MediaFileHandler {

  /**
   * @var FileAdapter
   */
  private $fileAdapter;

  /**
   * @var array
   */
  private $files = [];

  public function __construct(FileAdapter $fileAdapter) {
    $this->fileAdapter = $fileAdapter;
  }

  public function getFile(string $mediaItemKey): File {
    return $this->files[$mediaItemKey];
  }

  public function getFiles(string $mediaItemKey): array {
    return $this->files[$mediaItemKey]['files'];
  }

  public function addFile(File $file, string $mediaItemKey): void {
    $this->files[$mediaItemKey] = $file;
  }

  public function addToFiles(File $file, string $mediaItemKey): void {
    $this->files[$mediaItemKey]['files'][] = $file;
  }

  /**
   * Saves the files listed in the definitions as File entities.
   */
  public function saveFiles($mediaToGenerate, string $fixturesPath): void {
    foreach ($mediaToGenerate as $mediaItemKey => $mediaItem) {
      if (isset($mediaItem['file'])) {
        $file_data = $this->fileAdapter->fileGetContents($fixturesPath . '/' . $mediaItem['file']);
        if (($savedFile = $this->fileAdapter->fileSaveData($file_data, DEGOV_DEMO_CONTENT_FILES_SAVE_PATH . '/' . $mediaItem['file'])) !== FALSE) {
          $this->addFile($savedFile, $mediaItemKey);
        }
      }

      if (isset($mediaItem['files'])) {
        foreach ($mediaItem['files'] as $file) {
          $file_data = $this->fileAdapter->fileGetContents($fixturesPath . '/' . $file);
          if (($savedFile = $this->fileAdapter->fileSaveData($file_data, DEGOV_DEMO_CONTENT_FILES_SAVE_PATH . '/' . $file)) !== FALSE) {
            $this->addToFiles($savedFile, $mediaItemKey);
          }
        }

      }
    }
  }

  public function mapFileFields(&$media_item, string $customMediaKey): array {
    $fields = [];
    foreach ($media_item as $media_item_field_key => $media_item_field_value) {
      if ($media_item_field_key === 'file') {
        switch ($media_item['bundle']) {
          case 'image':
            $fields['image'] = [
              'target_id' => $this->getFile($customMediaKey)->id(),
              'alt'       => $media_item['name'],
              'title'     => $media_item['name'],
            ];
            break;

          case 'document':
            $fields['field_document'] = [
              'target_id' => $this->getFile($customMediaKey)->id(),
            ];
            break;

          case 'audio':
            $fields['field_audio_mp3'] = [
              'target_id' => $this->getFile($customMediaKey)->id(),
            ];
            break;

          case 'video_upload':
            $fields['field_video_upload_mp4'] = [
              'target_id' => $this->getFile($customMediaKey)->id(),
            ];
            break;
        }
        continue;
      }

      switch ($media_item_field_key) {
        case 'field_fullhd_video_mobile_mp4':
          $fields['field_fullhd_video_mobile_mp4'] = [
            'target_id' => $this->getFiles($customMediaKey)['field_fullhd_video_mobile_mp4']->id(),
          ];
          break;
        case 'field_hdready_video_mobile_mp4':
          $fields['field_hdready_video_mobile_mp4'] = [
            'target_id' => $this->getFiles($customMediaKey)['field_hdready_video_mobile_mp4']->id(),
          ];
          break;
        case 'field_mobile_video_mobile_mp4':
          $fields['field_mobile_video_mobile_mp4'] = [
            'target_id' => $this->getFiles($customMediaKey)['field_mobile_video_mobile_mp4']->id(),
          ];
          break;
        case 'field_video_mobile_mp4':
          $fields['field_video_mobile_mp4'] = [
            'target_id' => $this->getFiles($customMediaKey)['field_video_mobile_mp4']->id(),
          ];
          break;
        case 'field_ultrahd4k_video_mobile_mp4':
          $fields['field_ultrahd4k_video_mobile_mp4'] = [
            'target_id' => $this->getFiles($customMediaKey)['field_ultrahd4k_video_mobile_mp4']->id(),
          ];
          break;
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

    return $fields;
  }

}
