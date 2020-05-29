<?php

namespace Drupal\degov_paragraph_slideshow;

use Drupal\Component\Utility\Html;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class PreprocessParagraph.
 */
final class PreprocessParagraph implements ContainerInjectionInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * PreprocessParagraph constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager service.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, ModuleHandlerInterface $module_handler) {
    $this->entityTypeManager = $entityTypeManager;
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('module_handler')
    );
  }

  /**
   * Preprocess the paragraphs type "slide".
   *
   * @param array &$variables
   *   The theme preprocess function argument.
   */
  public function slide(array &$variables): void {
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
   * Preprocess the paragraphs type "slide".
   *
   * @param array &$variables
   *   The theme preprocess function argument.
   */
  public function slider(array &$variables): void {
    /** @var \Drupal\paragraphs\Entity\Paragraph $paragraph */
    $paragraph = $variables['paragraph'];
    // Add the variable slideshow_type.
    $variables['slideshow_type'] = $paragraph->field_slideshow_type->value;

    // Loop each paragraph in the slideshow field to propagate
    // referenced slides.
    if ($this->moduleHandler->moduleExists('degov_paragraph_node_reference')
      || $this->moduleHandler->moduleExists('degov_paragraph_view_reference')) {
      $propagated_slides = [];

      $paragraph_slides = $paragraph->field_slideshow_slides->referencedEntities();

      foreach ($paragraph_slides as $i => $paragraph_slide) {
        if ($paragraph_slide->hasField('field_node_reference_nodes')) {
          // Every node reference should be included as a single slide.
          foreach ($paragraph_slide->field_node_reference_nodes->referencedEntities() as $node) {
            $view_builder = $this->entityTypeManager->getViewBuilder('node');
            $propagated_slides[] = $view_builder->view($node, 'slideshow');
          }
        }
        elseif ($paragraph_slide->hasField('field_view_reference_view')) {
          // Every node in a view reference should be included as
          // a single slide.
          $result = views_get_view_result($paragraph_slide->field_view_reference_view->target_id, $paragraph_slide->field_view_reference_view->display_id);
          foreach ($result as $row) {
            $view_builder = $this->entityTypeManager->getViewBuilder('node');
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
