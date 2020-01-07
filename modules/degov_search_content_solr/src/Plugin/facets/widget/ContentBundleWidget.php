<?php

namespace Drupal\degov_search_content_solr\Plugin\facets\widget;

use Drupal\Core\Entity\EntityInterface;
use Drupal\degov_search_content_solr\InvalidContentBundleMachineNameException;
use Drupal\facets\FacetInterface;
use Drupal\facets\Plugin\facets\widget\DropdownWidget;

/**
 * The dropdown widget.
 *
 * @FacetsWidget(
 *   id = "content_bundle",
 *   label = @Translation("Content bundles"),
 *   description = @Translation("A configurable widget that shows a dropdown for content bundles."),
 * )
 */
class ContentBundleWidget extends DropdownWidget {

  /**
   * {@inheritdoc}
   */
  public function build(FacetInterface $facet) {
    $build = parent::build($facet);

    foreach ($build["#items"] as &$item) {
      $item = $this->transformBundleIdToName($item);
    }

    return $build;
  }

  /**
   * Transform bundle id to name.
   */
  private function transformBundleIdToName(array $item): array {
    $contentBundleMachineName = $item['#title']['#value'];

    try {

      if ($contentBundleMachineName !== 'document') {

        if (($entity = \Drupal::entityTypeManager()->getStorage('node_type')->load($contentBundleMachineName)) && $entity instanceof EntityInterface) {
          $item['#title']['#value'] = $entity->label();
        }
        else {
          throw new InvalidContentBundleMachineNameException($contentBundleMachineName);
        }

      }
      elseif (($entity = \Drupal::entityTypeManager()->getStorage('media_type')->load($contentBundleMachineName)) && $entity instanceof EntityInterface) {
        $item['#title']['#value'] = $entity->label();
      }
      else {
        throw new InvalidContentBundleMachineNameException($contentBundleMachineName);
      }

    }
    catch (\Exception $exception) {
      \Drupal::logger('degov_search_content_solr')->error($exception->getMessage() . ' - Passed content bundle machine name was: ' . $contentBundleMachineName);
    }

    return $item;
  }

}
