<?php

namespace Drupal\degov_demo_content;

use Drupal\file\Entity\File;

class FileAdapter {

  public function fileSaveData($fileData, string $filepath): ?File {

    $fileSaveData = file_save_data($fileData, $filepath, FILE_EXISTS_REPLACE);
    return ($fileSaveData === FALSE ? NULL : $fileSaveData);
  }

  public function fileGetContents(string $filepath): string {
    return file_get_contents($filepath);
  }

}
