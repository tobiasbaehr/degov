<?php

namespace Drupal\media_file_links\Service;

use Drupal\Core\Entity\Entity;
use Drupal\Core\Entity\EntityInterface;
use Drupal\media\Entity\Media;
use Drupal\media\MediaInterface;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\menu_link_content\MenuLinkContentInterface;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\paragraphs\ParagraphInterface;

/**
 * Class MediaFileLinkUsageTracker.
 *
 * @package Drupal\media_file_links\Service
 */
class MediaFileLinkUsageTracker {

  private $placeholderHandler;

  public function __construct(MediaFileLinkPlaceholderHandler $placeholder_handler) {
    $this->placeholderHandler = $placeholder_handler;
  }

  public function trackMediaUsage(EntityInterface $entity): void {
    if ($entity instanceof NodeInterface) {
      $this->trackMediaUsageInNode($entity);
    }

    if ($entity instanceof MenuLinkContentInterface) {
      $this->trackMediaUsageInMenuLinkContent($entity);
    }

    if ($entity instanceof ParagraphInterface) {
      $this->trackMediaUsageInParagraph($entity);
    }

    if ($entity instanceof MediaInterface) {
      $this->trackMediaUsageInMedia($entity);
    }
  }

  private function trackMediaUsageInNode(NodeInterface $node): void {
    foreach ($node->getFields() as $field) {
      $fieldValue = $field->getString();
      if ($this->placeholderHandler->isValidMediaFileLinkPlaceholder($fieldValue)) {
        $this->storeUsage($node->id(), 'node', $field->getName(), $node->get('langcode')->getString(), $this->placeholderHandler->getMediaIdFromPlaceholder($fieldValue));
      }
    }
  }

  private function trackMediaUsageInMenuLinkContent(MenuLinkContentInterface $menuLinkContent): void {
    $linkValue = $menuLinkContent->get('link')->getValue();
    if (!empty($linkValue[0]['uri']) && $this->placeholderHandler->isValidMediaFileLinkPlaceholder($linkValue[0]['uri'])) {
      $this->storeUsage($menuLinkContent->id(), 'menu_link_content', 'link', $menuLinkContent->get('langcode')->getString(), $this->placeholderHandler->getMediaIdFromPlaceholder($linkValue[0]['uri']));
    }
  }

  private function trackMediaUsageInParagraph(ParagraphInterface $paragraph): void {
    foreach ($paragraph->getFields() as $field) {
      $fieldValue = $field->getString();
      if ($this->placeholderHandler->isValidMediaFileLinkPlaceholder($fieldValue)) {
        $this->storeUsage($paragraph->id(), 'paragraph', $field->getName(), $paragraph->get('langcode')->getString(), $this->placeholderHandler->getMediaIdFromPlaceholder($fieldValue));
      }
    }
  }

  private function trackMediaUsageInMedia(MediaInterface $media): void {
    foreach ($media->getFields() as $field) {
      $fieldValue = $field->getString();
      if ($this->placeholderHandler->isValidMediaFileLinkPlaceholder($fieldValue)) {
        $this->storeUsage($media->id(), 'media', $field->getName(), $media->get('langcode')->getString(), $this->placeholderHandler->getMediaIdFromPlaceholder($fieldValue));
      }
    }
  }

  private function storeUsage(int $referencingEntityId, string $referencingEntityType, string $referencingEntityField, string $referencingEntityLangcode, int $mediaEntityId): void {
    $this->deletePriorUsages($referencingEntityId, $referencingEntityType, $referencingEntityField, $referencingEntityLangcode);

    \Drupal::database()
      ->insert('media_file_links_usage')
      ->fields([
        'referencing_entity_id'       => $referencingEntityId,
        'referencing_entity_type'     => $referencingEntityType,
        'referencing_entity_field'    => $referencingEntityField,
        'referencing_entity_langcode' => $referencingEntityLangcode,
        'media_entity_id'             => $mediaEntityId,
      ])
      ->execute();
  }

  private function deletePriorUsages(int $referencingEntityId, string $referencingEntityType, string $referencingEntityField, string $referencingEntityLangcode): void {
    \Drupal::database()
      ->delete('media_file_links_usage')
      ->condition('referencing_entity_id', $referencingEntityId)
      ->condition('referencing_entity_type', $referencingEntityType)
      ->condition('referencing_entity_field', $referencingEntityField)
      ->condition('referencing_entity_langcode', $referencingEntityLangcode)
      ->execute();
  }

  public function getUsagesByMediaIds(array $mediaIds): array {
    $queryResultsStatement = \Drupal::database()->select('media_file_links_usage', 'mflu')
      ->fields('mflu')
      ->condition('media_entity_id', $mediaIds, 'IN')
      ->execute();

    $usages = $queryResultsStatement->fetchAll(\PDO::FETCH_ASSOC);

    if(!empty($usages)) {
      foreach($usages as $usageKey => &$usage) {
        $usage['media_entity'] = Media::load($usage['media_entity_id']);

        $referencingEntity = null;
        switch($usage['referencing_entity_type']) {
          case 'media':
            $referencingEntity = Media::load($usage['referencing_entity_id']);
            break;
          case 'node':
            $referencingEntity = Node::load($usage['referencing_entity_id']);
            break;
          case 'paragraph':
            $referencingEntity = Paragraph::load($usage['referencing_entity_id']);
            break;
          case 'menu_link_content':
            $referencingEntity = MenuLinkContent::load($usage['referencing_entity_id']);
            break;
        }
        $usage['referencing_entity'] = $referencingEntity;
        $usage['referencing_entity_field_label'] = $referencingEntity->get($usage['referencing_entity_field'])->getFieldDefinition()->getLabel();
      }
    }

    return $usages;
  }

}
