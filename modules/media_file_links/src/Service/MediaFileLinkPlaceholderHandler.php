<?php

namespace Drupal\media_file_links\Service;

/**
 * Class MediaFileLinkPlaceholderHandler.
 *
 * @package Drupal\media_file_links\Service
 */
class MediaFileLinkPlaceholderHandler {

  public function isValidMediaFileLinkPlaceholder(string $placeholder): bool {
    return $this->getMediaIdFromPlaceholder($placeholder) !== NULL;
  }

  public function getMediaIdFromPlaceholder(string $placeholder): ?int {
    if(preg_match('/[\[<]media\/file\/([\d]+)[\]>]/', $placeholder, $matches) && !empty($matches[1]) && preg_match('/^[\d]+$/', $matches[1])) {
      return $matches[1];
    }

    return NULL;
  }

  public function getPlaceholderForMediaId(int $mediaId, $usage = 'node'): ?string {
    switch($usage) {
      case 'node':
      case 'paragraph':
        return sprintf('[media/file/%s]', $mediaId);
      case 'menu_link_content':
        return sprintf('<media/file/%s>', $mediaId);
    }
    return NULL;
  }

}
