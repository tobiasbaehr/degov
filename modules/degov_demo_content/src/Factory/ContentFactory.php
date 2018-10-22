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
   * Constructs a new ContentFactory instance.
   */
  public function __construct() {
    $this->moduleHandler = \Drupal::service('module_handler');
  }

  /**
   * Looks for a media.yml file and reads the date stored within.
   */
  protected function loadDefinitions($definitions_file_name) {
    $media_definitions_file_path = $this->moduleHandler->getModule('degov_demo_content')->getPath() . '/entity_definitions/' . $definitions_file_name;
    if(file_exists($media_definitions_file_path) && is_file($media_definitions_file_path) && is_readable($media_definitions_file_path)) {
      return Yaml::parseFile($media_definitions_file_path);
    }
  }

  public function generateContent() {}

  public function deleteContent() {}
}