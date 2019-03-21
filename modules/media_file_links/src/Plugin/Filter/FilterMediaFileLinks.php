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
    $placeholderHandler = \Drupal::service('media_file_links.placeholder_handler');
    while (($mediaId = $placeholderHandler->getMediaIdFromPlaceholder($text)) !== null) {
        $fileUrl = $linkResolver->getFileUrlString($mediaId);
        $text = str_replace($mediaId, $fileUrl, $text);
    }
    return new FilterProcessResult($text);
  }

}
