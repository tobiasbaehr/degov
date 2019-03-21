<?php

namespace Drupal\media_file_links\Service;

use Drupal\Core\Entity\EntityInterface;
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
  }

  private function trackMediaUsageInNode(NodeInterface $node): void {
    //    foreach($node->getFields() as $field) {
    //      \Drupal::service('media_file_links.usage_tracker')->trackUsage([
    //        'referencing_entity_id' => $node->id(),
    //        'referencing_entity_type' => 'node',
    //        'referencing_entity_langcode' => $node->get('langcode')->getString(),
    //        'media_entity_id' => $field->getString(),
    //      ]);
    //    }
  }

  private function trackMediaUsageInMenuLinkContent(MenuLinkContentInterface $menuLinkContent): void {
    $linkValue = $menuLinkContent->get('link')->getValue();
    if (!empty($linkValue[0]['uri']) && $this->placeholderHandler->isValidMediaFileLinkPlaceholder($linkValue[0]['uri'])) {
      $this->storeUsage([
        'referencing_entity_id'       => $menuLinkContent->id(),
        'referencing_entity_type'     => 'menu_link_content',
        'referencing_entity_field'    => 'link',
        'referencing_entity_langcode' => $menuLinkContent->get('langcode')->getString(),
        'media_entity_id'             => $this->placeholderHandler->getMediaIdFromPlaceholder($linkValue[0]['uri']),
      ]);
    }
  }

  private function trackMediaUsageInParagraph(ParagraphInterface $paragraph): void {
    //    foreach($node->getFields() as $field) {
    //      \Drupal::service('media_file_links.usage_tracker')->trackUsage([
    //        'referencing_entity_id' => $node->id(),
    //        'referencing_entity_type' => 'node',
    //        'referencing_entity_langcode' => $node->get('langcode')->getString(),
    //        'media_entity_id' => $field->getString(),
    //      ]);
    //    }
  }

  private function storeUsage(array $values): void {
    error_log(print_r($values, 1));
    \Drupal::database()->insert('media_file_links_usage')->fields($values)->execute();
  }

}
