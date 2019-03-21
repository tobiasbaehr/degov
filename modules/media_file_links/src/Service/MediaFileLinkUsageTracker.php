<?php

namespace Drupal\media_file_links\Service;

use Drupal\Core\Entity\EntityInterface;
use Drupal\media\MediaInterface;
use Drupal\menu_link_content\MenuLinkContentInterface;
use Drupal\node\NodeInterface;
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
        $this->storeUsage([
          'referencing_entity_id'       => $node->id(),
          'referencing_entity_type'     => 'node',
          'referencing_entity_field'    => $field->getName(),
          'referencing_entity_langcode' => $node->get('langcode')->getString(),
          'media_entity_id'             => $this->placeholderHandler->getMediaIdFromPlaceholder($fieldValue),
        ]);
      }
    }
  }

  private function trackMediaUsageInMenuLinkContent(MenuLinkContentInterface $menuLinkContent): void {
    $linkValue = $menuLinkContent->get('link')->getValue();
    if (!empty($linkValue[0]['uri']) && $this->placeholderHandler->isValidMediaFileLinkPlaceholder($linkValue[0]['uri'])) {
      $this->storeUsage([
        'referencing_entity_id'       => $menuLinkContent->id(),
        'referencing_entity_type'     => 'menu_link_content',
        'referencing_entity_field'    => 'link',
        'referencing_entity_langcode' => $menuLinkContent->get('langcode')
          ->getString(),
        'media_entity_id'             => $this->placeholderHandler->getMediaIdFromPlaceholder($linkValue[0]['uri']),
      ]);
    }
  }

  private function trackMediaUsageInParagraph(ParagraphInterface $paragraph): void {
    foreach ($paragraph->getFields() as $field) {
      $fieldValue = $field->getString();
      if ($this->placeholderHandler->isValidMediaFileLinkPlaceholder($fieldValue)) {
        $this->storeUsage([
          'referencing_entity_id'       => $paragraph->id(),
          'referencing_entity_type'     => 'paragraph',
          'referencing_entity_field'    => $field->getName(),
          'referencing_entity_langcode' => $paragraph->get('langcode')->getString(),
          'media_entity_id'             => $this->placeholderHandler->getMediaIdFromPlaceholder($fieldValue),
        ]);
      }
    }
  }

  private function trackMediaUsageInMedia(MediaInterface $media): void {
    foreach ($media->getFields() as $field) {
      $fieldValue = $field->getString();
      if ($this->placeholderHandler->isValidMediaFileLinkPlaceholder($fieldValue)) {
        $this->storeUsage([
          'referencing_entity_id'       => $media->id(),
          'referencing_entity_type'     => 'media',
          'referencing_entity_field'    => $field->getName(),
          'referencing_entity_langcode' => $media->get('langcode')->getString(),
          'media_entity_id'             => $this->placeholderHandler->getMediaIdFromPlaceholder($fieldValue),
        ]);
      }
    }
  }

  private function storeUsage(array $values): void {
    \Drupal::database()
      ->insert('media_file_links_usage')
      ->fields($values)
      ->execute();
  }

}
