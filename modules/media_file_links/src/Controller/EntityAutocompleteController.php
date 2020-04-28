<?php

namespace Drupal\media_file_links\Controller;

use Drupal\Core\KeyValueStore\KeyValueStoreInterface;
use Drupal\media_file_links\EntityAutocompleteMatcher;
use Drupal\system\Controller\EntityAutocompleteController as EntityAutocompleteControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class EntityAutocompleteController.
 */
class EntityAutocompleteController extends EntityAutocompleteControllerBase {

  /**
   * The autocomplete matcher for entity references.
   *
   * @var \Drupal\Core\Entity\EntityAutocompleteMatcher|\Drupal\media_file_links\EntityAutocompleteMatcher
   */
  protected $matcher;

  /**
   * EntityAutocompleteController constructor.
   */
  public function __construct(EntityAutocompleteMatcher $matcher, KeyValueStoreInterface $key_value) {
    $this->matcher = $matcher;
    $this->keyValue = $key_value;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('media_file_links.autocomplete_matcher'),
      $container->get('keyvalue')->get('entity_autocomplete')
    );
  }

}
