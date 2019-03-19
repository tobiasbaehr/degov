<?php

namespace Drupal\media_file_links\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\media_file_links\Controller\EntityAutocompleteController;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class AutocompleteRouteSubscriber.
 *
 * @package Drupal\media_file_links\Routing
 */
class AutocompleteRouteSubscriber extends RouteSubscriberBase {

  /**
   * @param \Symfony\Component\Routing\RouteCollection $collection
   */
  public function alterRoutes(RouteCollection $collection) {
    if ($route = $collection->get('system.entity_autocomplete')) {
      $route->setDefault('_controller', EntityAutocompleteController::class . '::handleAutocomplete');
    }
  }

}
