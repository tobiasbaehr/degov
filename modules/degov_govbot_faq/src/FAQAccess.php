<?php

namespace Drupal\degov_govbot_faq;

use Drupal\entity_reference_revisions\EntityReferenceRevisionsFieldItemList;
use Drupal\node\NodeInterface;
use Drupal\paragraphs\Entity\Paragraph;


class FAQAccess {

  /**
   * @var ParagraphsExtractor
   */
  private $paragraphsExtractor;

  public function __construct(ParagraphsExtractor $paragraphsExtractor)
  {
    $this->paragraphsExtractor = $paragraphsExtractor;
  }

  public function isAccessibleOnSite(NodeInterface $node): bool {
    $accessResult = TRUE;
    $faqListParagraphs = $this->paragraphsExtractor->getFAQListParagraphs($node);

    if ($node->getType() === 'faq') {

      foreach ($faqListParagraphs as $faqListParagraph) {
        if ($faqListParagraph instanceof Paragraph && ($fieldFAQListInnerParagraphs = $faqListParagraph->get('field_faq_list_inner_paragraphs')) instanceof EntityReferenceRevisionsFieldItemList) {

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

}
