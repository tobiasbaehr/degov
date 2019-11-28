<?php

namespace Drupal\degov_demo_content;

use Drupal\file\Entity\File;


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

  public function getFile(string $mediaItemKey): ?File {
    return $this->files[$mediaItemKey];
  }

  public function getFiles(string $mediaItemKey): ?array {
    return $this->files[$mediaItemKey]['files'];
  }

  public function addFile(File $file, string $mediaItemKey): void {
    $this->files[$mediaItemKey] = $file;
  }

  public function addToFiles(File $file, string $mediaItemKey, string $fieldName): void {
    $this->files[$mediaItemKey]['files'][$fieldName] = $file;
  }

  /**
   * Saves the files listed in the definitions as File entities.
   */
  public function saveFiles(array $mediaToGenerate, string $fixturesPath): void {
    foreach ($mediaToGenerate as $mediaItemKey => $mediaItem) {
      if (isset($mediaItem['file'])) {
        if ($savedFile = $this->fileAdapter->fileSaveData(
          $this->fileAdapter->fileGetContents($fixturesPath . '/' . $mediaItem['file']),
          DEGOV_DEMO_CONTENT_FILES_SAVE_PATH . '/' . $mediaItem['file'])
        ) {
          $this->addFile($savedFile, $mediaItemKey);
        }
      }

      if (isset($mediaItem['files'])) {
        foreach ($mediaItem['files'] as $fieldName => $fileName) {
          if ($savedFile = $this->fileAdapter->fileSaveData(
            $this->fileAdapter->fileGetContents($fixturesPath . '/' . $fileName),
            DEGOV_DEMO_CONTENT_FILES_SAVE_PATH . '/' . $fileName)
          ) {
            $this->addToFiles($savedFile, $mediaItemKey, $fieldName);
          }
        }

      }
    }
  }

  public function mapFileFields(&$media_item, string $customMediaKey): array {
    $fields = [];
    foreach ($media_item as $media_item_field_key => $media_item_field_value) {
      if ($media_item_field_key === 'file' && $this->getFile($customMediaKey) !== NULL) {
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
      } elseif ($media_item_field_key === 'files' && $this->getFiles($customMediaKey) !== NULL) {
        foreach ($media_item_field_value as $fileFieldName => $fileName) {
          switch ($fileFieldName) {
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
        }

      } else {
        $fields[$media_item_field_key] = $media_item_field_value;
      }

    }

    return $fields;
  }

}
