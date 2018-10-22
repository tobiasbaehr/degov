<?php

namespace Drupal\degov_demo_content\Factory;

use Drupal\taxonomy\Entity\Term;
use Symfony\Component\Yaml\Yaml;

class ContentFactory {

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The name of the vocabulary we are tagging our content with.
   *
   * @var string
   */
  protected $tagsVocabularyName = 'tags';

  /**
   * The name of the tag we are giving all our content.
   *
   * @var string
   */
  protected $demoContentTagName = 'degov_demo_content';

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
    $tag_term = taxonomy_term_load_multiple_by_name($this->demoContentTagName, $this->tagsVocabularyName);
    if (empty($tag_term)) {
      $tag_term = Term::create([
        'name' => $this->demoContentTagName,
        'vid'  => $this->tagsVocabularyName,
      ]);
      $tag_term->save();
    }
    else {
      $tag_term = reset($tag_term);
    }
    return $tag_term->id();
  }
}