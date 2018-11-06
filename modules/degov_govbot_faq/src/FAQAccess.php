<?php

namespace Drupal\degov_govbot_faq;

use Drupal\Core\Field\FieldItemList;
use Drupal\entity_reference_revisions\EntityReferenceRevisionsFieldItemList;
use Drupal\node\NodeInterface;
use Drupal\paragraphs\Entity\Paragraph;

class FAQAccess {

  public function isAccessibleOnSite(NodeInterface $node): bool {
    $accessResult = TRUE;

    $faqListParagraphs = $this->getFAQListParagraphs($node);

    foreach ($faqListParagraphs as $faqListParagraph) {
      if ($faqListParagraph instanceof Paragraph) {

        if (($fieldFAQListInnerParagraphs = $faqListParagraph->get('field_faq_list_inner_paragraphs')) instanceof EntityReferenceRevisionsFieldItemList) {

          $paragraphFAQItems = $fieldFAQListInnerParagraphs->getValue();

          foreach ($paragraphFAQItems as $paragraphFAQItem) {
            $paragraphFAQItemEntity = Paragraph::load($paragraphFAQItem['target_id']);

            $fieldFAQText = $paragraphFAQItemEntity->get('field_faq_text')->getValue();
            $fieldFAQTitle = $paragraphFAQItemEntity->get('field_faq_title')->getValue();

            if (empty($fieldFAQText['0']['value']) || empty($fieldFAQTitle['0']['value'])) {
              return FALSE;
            }

          }
        }
      }

    }

    return $accessResult;
  }

  private function getFAQListParagraphs(NodeInterface $node) {
    $referencedParagraphs = [];

    foreach ($node->getFields() as $field) {
      if ($field->getDataDefinition()->getType() === 'entity_reference_revisions' && $field->getDataDefinition()->get('settings')['handler'] === 'default:paragraph') {
        foreach ($field->getValue() as $paragraphReference) {
          $referencedParagraph = Paragraph::load($paragraphReference['target_id']);
          if ($referencedParagraph->getType() === 'faq_list') {
            $referencedParagraphs[] = $referencedParagraph;
          }
        }
      }
    }

    return $referencedParagraphs;
  }

}
