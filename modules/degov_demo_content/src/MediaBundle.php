<?php

namespace Drupal\degov_demo_content;

use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;

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

}
