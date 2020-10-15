<?php

declare(strict_types=1);

namespace Drupal\degov_govbot_faq;

use Drupal\node\NodeInterface;

/**
 * Interface ParagraphsExtractorInterface
 *
 * @package Drupal\degov_govbot_faq
 */
interface ParagraphsExtractorInterface {

  /**
   * Get faq list paragraphs.
   *
   * @param \Drupal\node\NodeInterface $node
   *   Node to get from.
   *
   * @return \Drupal\paragraphs\ParagraphInterface[]
   *   Referenced paragraphs.
   */
  public function getFaqListParagraphs(NodeInterface $node): array;

}
