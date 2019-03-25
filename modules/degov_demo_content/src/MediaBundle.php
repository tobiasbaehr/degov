<?php

namespace Drupal\degov_demo_content;

use Drupal\Core\Entity\EntityFieldManager;

class MediaBundle {

  /**
   * @var \Drupal\Core\Entity\EntityFieldManager
   */
  private $entityFieldManager;

  public function __construct(EntityFieldManager $entityFieldManager) {
    $this->entityFieldManager = $entityFieldManager;
  }

  public function bundleHasField(string $fieldName, string $bundle): bool {
    $fields = $this->entityFieldManager->getFieldDefinitions('media', $bundle);
    if (\in_array($fieldName, array_keys($fields), TRUE)) {
      return TRUE;
    }

    return FALSE;
  }

}
