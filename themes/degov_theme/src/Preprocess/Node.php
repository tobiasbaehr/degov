<?php
/**
 * @file MediaGallery.php.
 */

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
  static public function preprocess(array &$variables) {
    /** @var \Drupal\node\Entity\Node $node */
    $node = $variables['node'];
    // Add created time to the search teaser template.
    if ($variables['view_mode'] == 'teaser') {
      $variables['bundle'] = $variables['node']->type->entity->label();
      $variables['date'] = \Drupal::service('date.formatter')
        ->format($node->getCreatedTime(), 'custom', 'd.m.Y');
    }

    // The configuration for "event" content type doens't use the responsive
    // image we need in our teaser. Set it here.
    if ($node->bundle() === 'event') {
      $responsive_image_style_id = '';
      if (in_array($variables['view_mode'], ['long_text', 'preview',])) {
        $responsive_image_style_id = 'teaser_squared';
      }
      elseif (in_array($variables['view_mode'], ['small_image', 'slim',])) {
        $responsive_image_style_id = 'teaser_landscape';
      }
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
  }

}
