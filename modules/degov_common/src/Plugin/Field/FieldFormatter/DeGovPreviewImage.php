<?php

namespace Drupal\degov_common\Plugin\Field\FieldFormatter;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Field\EntityReferenceFieldItemListInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceEntityFormatter;
use Drupal\Core\TypedData\TranslatableInterface;

/**
 * Plugin implementation of the 'entity reference rendered entity' formatter.
 *
 * @FieldFormatter(
 *   id = "degov_preview_image",
 *   label = @Translation("Rendered entity or substitution"),
 *   description = @Translation("Display the referenced entities rendered by entity_view()."),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class DeGovPreviewImage extends EntityReferenceEntityFormatter {

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    // This formatter is only available for entity types that have a view
    // builder.
    $target_type = $field_definition->getFieldStorageDefinition()->getSetting('target_type');
    $hasViewBuilderClass = \Drupal::entityManager()->getDefinition($target_type)->hasViewBuilderClass();
    if ($target_type == 'media' && $hasViewBuilderClass) {
      $settings = $field_definition->getSettings();
      if (!empty($settings['handler_settings']['target_bundles']) && in_array('image', $settings['handler_settings']['target_bundles'])) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  protected function getEntitiesToView(EntityReferenceFieldItemListInterface $items, $langcode) {
    $entities = parent::getEntitiesToView($items, $langcode);
    // If empty entities, try to load the one from header paragraphs.
    if (empty($entities)) {
      // Get the parent entity.
      $parentEntity = $items->getEntity();
      // Check if there is field_header_paragraphs and if it is not empty.
      if ($parentEntity->hasField('field_header_paragraphs') && !$parentEntity->get('field_header_paragraphs')->isEmpty()) {
        $header_paragraphs = $parentEntity->get('field_header_paragraphs')->getValue();
        $media = FALSE;
        // Loop through the values to find the header media type.
        foreach ($header_paragraphs as $header_paragraph) {
          $paragraph = \Drupal::entityManager()->getStorage('paragraph')->load($header_paragraph['target_id']);
          if ($paragraph && $paragraph->bundle() == 'image_header' && !$paragraph->get('field_header_media')->isEmpty()) {
            $media = $paragraph->get('field_header_media')->entity;
            break;
          }
        }
        if ($media) {
          $entity = $media;

          // Set the entity in the correct language for display.
          if ($entity instanceof TranslatableInterface) {
            $entity = \Drupal::entityManager()->getTranslationFromContext($entity, $langcode);
          }

          $access = $this->checkAccess($entity);
          // Add the access result's cacheability, ::view() needs it.
          $media->_accessCacheability = CacheableMetadata::createFromObject($access);
          if ($access->isAllowed()) {
            // Add the referring item, in case the formatter needs it.
            $items->set(0, $entity);
            $entity->_referringItem = $items[0];
            $entities[0] = $entity;
          }
        }
      }
    }

    return $entities;
  }

}
