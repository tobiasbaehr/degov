<?php

declare(strict_types=1);

namespace Drupal\degov_paragraph_view_reference\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\views\Views;

/**
 * Field formatter for Viewsreference Field.
 *
 * @FieldFormatter(
 *   id = "degov_viewsreference_label",
 *   label = @Translation("Views Label"),
 *   field_types = {"viewsreference"}
 * )
 */
class ViewsReferenceLabelFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    foreach ($items as $delta => $item) {
      $view_name = $item->getValue()['target_id'];
      $view = Views::getView($view_name);
      // Someone may have deleted the View.
      if ($view === NULL) {
        continue;
      }
      $title = $view->getTitle();
      $title_render_array = [
        '#theme' => 'viewsreference__view_title',
        '#title' => $this->t($title),
      ];
      $elements[$delta]['title'] = $title_render_array;
    }
    return $elements;
  }

}
