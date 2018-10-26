<?php

namespace Drupal\degov_paragraph_slideshow;

use Drupal\Component\Utility\Html;
use Drupal\Core\Url;

class PreprocessParagraph {

  /**
   * @param $variables
   */
  public function slide(&$variables): void {
    /** @var \Drupal\paragraphs\Entity\Paragraph $paragraph */
    $paragraph = $variables['paragraph'];
    // Add the variable slide_link.
    $variables['slide_link'] = FALSE;
    if ($paragraph->field_slide_link->uri) {
      $url = Url::fromUri($paragraph->field_slide_link->uri);
      $variables['slide_link'] = $url;
    }
  }

  /**
   * @param $variables.
   */
  public function slider(&$variables): void {
    /** @var \Drupal\paragraphs\Entity\Paragraph $paragraph */
    $paragraph = $variables['paragraph'];
    // Add the variable slideshow_type.
    $variables['slideshow_type'] = $paragraph->field_slideshow_type->value;

    $moduleHandler = \Drupal::service('module_handler');
    // Loop each paragraph in the slideshow field to propagate referenced slides.
    if ($moduleHandler->moduleExists('degov_paragraph_node_reference')
      || $moduleHandler->moduleExists('degov_paragraph_view_reference')) {
      $propagated_slides = [];

      $paragraph_slides = $paragraph->field_slideshow_slides->referencedEntities();

      foreach ($paragraph_slides as $i => $paragraph_slide) {
        if ($paragraph_slide->hasField('field_node_reference_nodes')) {
          // Every node reference should be included as a single slide.
          foreach ($paragraph_slide->field_node_reference_nodes->referencedEntities() as $node) {
            $view_builder = \Drupal::entityManager()->getViewBuilder('node');
            $propagated_slides[] = $view_builder->view($node, 'slideshow');
          }
        }
        elseif ($paragraph_slide->hasField('field_view_reference_view')) {
          // Every node in a view reference should be included as a single slide.
          $result = views_get_view_result($paragraph_slide->field_view_reference_view->target_id, $paragraph_slide->field_view_reference_view->display_id);
          foreach ($result as $row) {
            $view_builder = \Drupal::entityManager()->getViewBuilder('node');
            $propagated_slides[] = $view_builder->view($row->_entity, 'slideshow');
          }
        }
        else {
          // Normal paragraph slides will be included back in the slideshow.
          $propagated_slides[] = $variables['content']['field_slideshow_slides'][$i];
        }
        // Unset each slide, as last step will propagate all slides again.
        unset($variables['content']['field_slideshow_slides'][$i]);
      }
      $variables['content']['field_slideshow_slides'] += $propagated_slides;
    }

    $variables['slider_id'] = Html::getUniqueId('degov-slider');
  }

}
