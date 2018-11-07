<?php

namespace Drupal\degov_govbot_faq;

use Drupal\entity_reference_revisions\EntityReferenceRevisionsFieldItemList;
use Drupal\node\NodeInterface;
use Drupal\paragraphs\Entity\Paragraph;


class GovBotFieldsExtractor {

  public function compute(NodeInterface $node): array
  {
    $faqListParagraphs = $this->getFAQListParagraphs($node);

    $govBotFields = [];


    foreach ($faqListParagraphs as $faqListParagraph) {
      if ($faqListParagraph instanceof Paragraph) {

        if (($fieldFAQListInnerParagraphs = $faqListParagraph->get('field_faq_list_inner_paragraphs')) instanceof EntityReferenceRevisionsFieldItemList) {

          $paragraphFAQItems = $fieldFAQListInnerParagraphs->getValue();

          foreach ($paragraphFAQItems as $paragraphFAQItem) {
            $paragraphFAQItemEntity = Paragraph::load($paragraphFAQItem['target_id']);

            $fieldGovBotAnswer = $paragraphFAQItemEntity->get('field_govbot_answer')->getValue();
            $fieldGovBotQuestion = $paragraphFAQItemEntity->get('field_govbot_question')->getValue();

            if (!empty($fieldGovBotAnswer['0']['value']) && !empty($fieldGovBotQuestion['0']['value'])) {
              $govBotFields[] = new GovBotFieldsModel($fieldGovBotAnswer['0']['value'], $fieldGovBotQuestion['0']['value']);
            }

          }
        }
      }

    }

    return $govBotFields;
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
