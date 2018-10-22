<?php

namespace Drupal\degov_demo_content\Factory;

use Symfony\Component\Yaml\Yaml;

class ContentFactory {

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The entity type we are working with.
   *
   * Overridden in the type-specific implementations.
   *
   * @var string
   */
  protected $entityType = '';

  /**
   * Constructs a new ContentFactory instance.
   */
  public function __construct() {
    $this->moduleHandler = \Drupal::service('module_handler');
  }

  /**
   * Looks for a media.yml file and reads the date stored within.
   */
  protected function loadDefinitions($definitions_file_name) {
    $media_definitions_file_path = $this->moduleHandler->getModule('degov_demo_content')
        ->getPath() . '/entity_definitions/' . $definitions_file_name;
    if (file_exists($media_definitions_file_path) && is_file($media_definitions_file_path) && is_readable($media_definitions_file_path)) {
      return Yaml::parseFile($media_definitions_file_path);
    }
  }

  /**
   * @return int|null|string
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function getDemoContentTagId() {
    $tag_term = taxonomy_term_load_multiple_by_name(DEGOV_DEMO_CONTENT_TAG_NAME, DEGOV_DEMO_CONTENT_TAGS_VOCABULARY_NAME);
    if(!empty($tag_term)) {
      $tag_term = reset($tag_term);
      return $tag_term->id();
    }
    return null;
  }

  /**
   * Deletes the generated entities.
   */
  public function deleteContent() {
    $entities = \Drupal::entityTypeManager()->getStorage($this->entityType)->loadByProperties([
      'field_tags' => $this->getDemoContentTagId(),
    ]);

    foreach($entities as $entity) {
      $entity->delete();
    }
  }
}