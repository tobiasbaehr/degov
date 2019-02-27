<?php

namespace Drupal\media_file_links\Plugin\Filter;

use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;

/**
 * Transforms placeholders with Media IDs to the corresponding file links.
 *
 * @Filter(
 *   id = "filter_mediafilelinks",
 *   title = @Translation("Resolve links to media files"),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_MARKUP_LANGUAGE,
 * )
 */
class FilterMediaFileLinks extends FilterBase {

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    $linkResolver = \Drupal::service('media_file_links.file_link_resolver');
    while (preg_match('/\[media\:file\:(\d+)\]/', $text, $matches)) {
      if (!empty($matches[1]) && is_numeric($matches[1])) {
        $fileUrl = $linkResolver->getFileUrlString($matches[1]);
        $text = str_replace($matches[0], $fileUrl, $text);
      }
    }
    return new FilterProcessResult($text);
  }

}
