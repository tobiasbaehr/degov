<?php

declare(strict_types=1);

namespace Drupal\degov_govbot_faq;

use Drupal\node\NodeInterface;
use Drupal\paragraphs\Entity\Paragraph;

/**
 * Class ParagraphsExtractor.
 */
class ParagraphsExtractor implements ParagraphsExtractorInterface {

  /**
   * {@inheritdoc}
   */
  public function getFaqListParagraphs(NodeInterface $node): array {
    $referencedParagraphs = [];

    foreach ($node->getFields() as $field) {
      if ($field->getDataDefinition()->getType() === 'entity_reference_revisions' && $field->getDataDefinition()->get('settings')['handler'] === 'default:paragraph') {
        foreach ($field->getValue() as $paragraphReference) {
          $referencedParagraph = Paragraph::load($paragraphReference['target_id']);
          if ($referencedParagraph && $referencedParagraph->getType() === 'faq_list') {
            $referencedParagraphs[] = $referencedParagraph;
          }
        }
      }
    }

    return $referencedParagraphs;
  }

}
