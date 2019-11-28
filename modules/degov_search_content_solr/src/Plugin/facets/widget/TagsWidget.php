<?php

namespace Drupal\degov_search_content_solr\Plugin\facets\widget;

use Drupal\facets\FacetInterface;
use Drupal\facets\Plugin\facets\widget\DropdownWidget;

/**
 * The dropdown widget.
 *
 * @FacetsWidget(
 *   id = "tags",
 *   label = @Translation("Tags"),
 *   description = @Translation("A configurable widget that shows a dropdown for tags."),
 * )
 */
class TagsWidget extends DropdownWidget {

  /**
   * {@inheritdoc}
   */
  public function build(FacetInterface $facet) {
    $build = parent::build($facet);

    foreach ($build["#items"] as &$item) {
    	if ($transformedItem = $this->transformTidToName($item)) {
				$item = $transformedItem;
			}
    }

    return $build;
  }

  private function transformTidToName(array $item): ?array {
    $resultSet = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties([
        'vid' => 'tags',
        'tid' => $item['#title']['#value']
      ]);

    if (!empty($resultSet)) {
			$result = array_shift($resultSet);

			$item['#title']['#value'] = $result->getName();

			return $item;
		}

    return null;
  }

}
