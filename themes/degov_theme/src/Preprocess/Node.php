<?php

namespace Drupal\degov_theme\Preprocess;

/**
 * Class Node.
 *
 * @package Drupal\degov_theme\Preprocess
 */
class Node {

  /**
   * Preprocess node theme.
   *
   * @param array $variables
   */
  public function preprocess(array &$variables): void {
    /** @var \Drupal\node\Entity\Node $node */
    $node = $variables['node'];
    // Add created time to the search teaser template.
    if ($variables['view_mode'] == 'teaser') {
      $variables['bundle'] = $variables['node']->type->entity->label();
      $variables['date'] = \Drupal::service('date.formatter')
        ->format($node->getCreatedTime(), 'custom', 'd.m.Y');
    }

    // The configuration for "event" content type doesn't use the responsive
    // image we need in our teaser. Set it here.
    if ($node->bundle() === 'event') {
      $responsive_image_style_id = $this->determineResponsiveImageStyle($variables['view_mode']);
      if ($medias = $node->get('field_teaser_image')->referencedEntities()) {
        /** @var \Drupal\media\Entity\Media $media */
        $media = reset($medias);
        $variables['content']['field_teaser_image'] = [
          '#type' => 'responsive_image',
          '#responsive_image_style_id' => $responsive_image_style_id,
          '#uri' => $media->image->entity->getFileUri(),
        ];
      }
    }

    // Special treatment for media_reference with video_mobile Media.
    if ($node->hasField('field_content_paragraphs')
      && !$node->field_content_paragraphs->isEmpty()) {
      $responsive_image_style_id = $this->determineResponsiveImageStyle($variables['view_mode']);
      $paragraphs = $node->get('field_content_paragraphs')->referencedEntities();
      if (is_array($paragraphs) && count($paragraphs)) {
        /** @var \Drupal\paragraphs\Entity\Paragraph $paragraph */
        foreach ($paragraphs as $paragraph) {
          if ($paragraph->bundle() === 'media_reference') {
            if (!$paragraph->get('field_media_reference_media')->isEmpty()) {
              /** @var \Drupal\media\Entity\Media $media */
              $video_media = $paragraph->get('field_media_reference_media')->entity;
              if ($video_media->bundle() === 'video_mobile') {
                if (!$video_media->get('field_video_mobile_preview')->isEmpty()) {
                  /** @var \Drupal\media\Entity\Media $image_media */
                  $image_media = $video_media->get('field_video_mobile_preview')->entity;
                  $variables['content']['field_teaser_image'] = [
                    '#type' => 'responsive_image',
                    '#responsive_image_style_id' => $responsive_image_style_id,
                    '#uri' => $image_media->image->entity->getFileUri(),
                  ];
                }
              }
            }
          }
        }
      }
    }
  }

  /**
   * Helper function to determine which image style to use.
   *
   * Determine responsive image style for particular view mode.
   *
   * @param string $view_mode_name
   *   The view mode to test.
   *
   * @return string
   */
  protected function determineResponsiveImageStyle(string $view_mode_name): string {
    $responsive_image_style_id = '';
    if (in_array($view_mode_name, [
      'long_text',
      'preview',
      'small_image',
    ])) {
      $responsive_image_style_id = 'teaser_squared';
    }
    elseif ($view_mode_name === 'slim') {
      $responsive_image_style_id = 'teaser_landscape';
    }
    return $responsive_image_style_id;
  }

}
