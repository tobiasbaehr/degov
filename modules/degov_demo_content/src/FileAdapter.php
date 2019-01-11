<?php

namespace Drupal\degov_demo_content;

class FileAdapter {

  /**
   * @return bool|int
   */
  public function fileSaveData(string $fileData, string $filename) {
    return file_save_data($fileData, DEGOV_DEMO_CONTENT_FILES_SAVE_PATH . '/' . $filename, FILE_EXISTS_REPLACE);
  }

  public function fileGetContents(string $filepath): string {
    return file_get_contents($filepath);
  }

}
