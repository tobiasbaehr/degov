<?php

namespace Drupal\degov_media_file_links\Service;

use Drupal\media\Entity\Media;

/**
 * Class MediaFileLinkSuggester.
 *
 * Accepts a (partial) search string and finds matching Media by title and filename.
 *
 * @package Drupal\degov_media_file_links\Service
 */
class MediaFileSuggester {

  public function __construct() {
  }

  public function findBySearchString(string $search): array {
    $this->findBySearchInTitle($search);
    $this->findBySearchInFilename($search);
  }

  private function findBySearchInTitle(string $search): array {
    return [];
  }

  private function findBySearchInFilename(string $search): array {
    return [];
  }
}
