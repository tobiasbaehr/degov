<?php

namespace Drupal\degov_demo_content;

use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\field\Entity\FieldConfig;

class MediaBundle {

  private $entityFieldManager;

  private $entityTypeBundleInfo;

  public function __construct(EntityFieldManagerInterface $entityFieldManager, EntityTypeBundleInfoInterface $entityTypeBundleInfo) {
    $this->entityFieldManager = $entityFieldManager;
    $this->entityTypeBundleInfo = $entityTypeBundleInfo;
  }

  public function bundleHasField(string $fieldName, string $bundle): bool {
    $fields = $this->entityFieldManager->getFieldDefinitions('media', $bundle);
    if (\array_key_exists($fieldName, array_flip(array_keys($fields)))) {
      return TRUE;
    }

    return FALSE;
  }

  public function bundleExistsInStorage(string $bundle): bool {
    $allBundles = $this->entityTypeBundleInfo->getBundleInfo('media');
    if (\array_key_exists($bundle, array_flip(array_keys($allBundles)))) {
      return TRUE;
    }

    return FALSE;
  }

  public function computeReferenceFieldArray(array $mediaItem, string $mediaItemKey, array $files): array {
    /**
     * @var FieldConfig $fieldDefinitions
     */
    $fieldDefinitions = $this->entityFieldManager->getFieldDefinitions('media', $mediaItem['bundle']);

    foreach ($fieldDefinitions as $fieldName => $fieldDefinition) {
      if ($mediaItem['bundle'] === 'image') {
        $field[$mediaItem['file']['field_name']] = [
          'target_id' => $files[$mediaItemKey]->id(),
          'alt'       => $mediaItem['name'],
          'title'     => $mediaItem['name'],
        ];

        return $field;
      }

      if ($fieldDefinition->getType() === 'entity_reference') {
        $field[$mediaItem['file']['field_name']] = [
          'target_id' => $files[$mediaItemKey]->id(),
        ];

        return $field;
      }

    }
  }



}
