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
   * Counter for the word generation. Makes generated content more static
   *
   * @var int
   */
  private $textCounter = 0;

  /**
   * Base string for text generation
   *
   * @var string
   */
  private const blindText = 'Lorem ipsum dolor sit amet consetetur sadipscing elitr sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat sed diam voluptua At vero eos et accusam et justo duo dolores et ea rebum Stet clita kasd gubergren no sea takimata sanctus est Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet consetetur sadipscing elitr sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat sed diam voluptua At vero eos et accusam et justo duo dolores et ea rebum Stet clita kasd gubergren no sea takimata sanctus est Lorem ipsum dolor sit amet';

  /**
   * Constructs a new ContentFactory instance.
   */
  public function __construct() {
    $this->moduleHandler = \Drupal::service('module_handler');
  }

  /**
   * Looks for a file and reads the date stored within.
   */
  protected function loadDefinitions($definitions_file_name) {
    $definitions_file_path = $this->moduleHandler->getModule('degov_demo_content')
        ->getPath() . '/entity_definitions/' . $definitions_file_name;
    if (file_exists($definitions_file_path) && is_file($definitions_file_path) && is_readable($definitions_file_path)) {
      return Yaml::parseFile($definitions_file_path);
    }
  }

  /**
   * @return int|null|string
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function getDemoContentTagId() {
    $tag_term = taxonomy_term_load_multiple_by_name(DEGOV_DEMO_CONTENT_TAG_NAME, DEGOV_DEMO_CONTENT_TAGS_VOCABULARY_NAME);
    if (!empty($tag_term)) {
      $tag_term = reset($tag_term);
      return $tag_term->id();
    }
    return NULL;
  }

  /**
   * @return int|null|string
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function getDemoContentCopyrightId() {
    $tag_term = taxonomy_term_load_multiple_by_name(DEGOV_DEMO_CONTENT_TAG_NAME, DEGOV_DEMO_CONTENT_COPYRIGHT_VOCABULARY_NAME);
    if (!empty($tag_term)) {
      $tag_term = reset($tag_term);
      return $tag_term->id();
    }
    return NULL;
  }

  /**
   * Deletes the generated entities.
   */
  public function deleteContent() {
    if ($this->getDemoContentTagId() === NULL) {
      return;
    }
    $entities = \Drupal::entityTypeManager()
      ->getStorage($this->entityType)
      ->loadByProperties([
        'field_tags' => $this->getDemoContentTagId(),
      ]);

    foreach ($entities as $entity) {
      $entity->delete();
    }
  }


  public function generateBlindText(int $wordCount): string {
    $phrase = [];
    for ($i = 0; $i < $wordCount; $i++) {
      $phrase[] = $this->getWord();
    }
    return implode(' ', $phrase);
  }

  protected function getWord(): string {
    $words = explode(' ', self::blindText);
    $this->textCounter++;
    $index = $this->textCounter % count($words);
    return $words[$index];
  }

  protected function loadDefinitionByNameTag(string $defName, $tag) {
    $def = $this->loadDefinitions($defName . '.yml');
    return $def[$tag];
  }

  protected function loadDefinitionByNameType(string $defName, string $type) {
    $def = $this->loadDefinitions($defName . '.yml');
    return array_filter($def, function ($var) use ($type) {
      return $var['type'] === $type;
    });
  }
}
