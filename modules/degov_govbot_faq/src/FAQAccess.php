<?php

namespace Drupal\degov_govbot_faq;

use Drupal\node\NodeInterface;
use Drupal\paragraphs\Entity\Paragraph;

class FAQAccess {

  public function isAccessibleOnSite(NodeInterface $node): bool {
    $accessResult = TRUE;

    $fieldFAQRelated = $node->get('field_faq_related');

    if ($fieldFAQRelated->getFieldDefinition()->get('field_type') !== 'entity_reference_revisions') {
      throw new \LogicException('field_faq_related must be of type entity_reference_revisions to support paragraphs.');
    }

    $paragraphFAQListItems = $fieldFAQRelated->getValue();

    foreach ($paragraphFAQListItems as $paragraphFAQListItem) {
      $paragraphFAQListItemEntity = Paragraph::load($paragraphFAQListItem['target_id']);
      $paragraphFAQItems = $paragraphFAQListItemEntity->get('field_faq_list_inner_paragraphs')->getValue();

      foreach ($paragraphFAQItems as $paragraphFAQItem) {
        $paragraphFAQItemEntity = Paragraph::load($paragraphFAQItem['target_id']);

        $fieldFAQText = $paragraphFAQItemEntity->get('field_faq_text')->getValue();
        $fieldFAQTitle = $paragraphFAQItemEntity->get('field_faq_title')->getValue();

        if (empty($fieldFAQText['0']['value']) || empty($fieldFAQTitle['0']['value'])) {
          return FALSE;
        }

      }

    }

    return $accessResult;
  }

}
