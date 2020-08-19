<?php

declare(strict_types=1);

namespace Drupal\degov_paragraph_view_reference\Plugin\Field\FieldType;

use Drupal\viewsreference\Plugin\Field\FieldType\ViewsReferenceItem;

/**
 * Class ViewsReferenceOverride.
 *
 * @package Drupal\degov_paragraph_view_reference\Plugin\Field\FieldType
 */
class ViewsReferenceOverride extends ViewsReferenceItem {

  /**
   * {@inheritdoc}
   */
  public function setValue($values, $notify = TRUE) {
    $data = (array_key_exists('data', $values) && is_string($values['data'])) ? unserialize($values['data'], ['allowed_classes' => FALSE]) : [];
    $arguments = $data['argument'] ?? NULL;
    if (is_array($arguments)) {
      $data['argument'] = implode('/', $arguments);
      $values['data'] = serialize($data);
    }
    parent::setValue($values, FALSE);
  }

}
