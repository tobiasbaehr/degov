<?php

namespace Drupal\degov_demo_content\Factory;

class NodeFactory extends ContentFactory {

  /**
   * The entity type we are working with.
   *
   * @var string
   */
  protected $entityType = 'node';

  public function __construct() {
    parent::__construct();
  }

  /**
   * Generates a set of node entities.
   */
  public function generateContent() {}

  /**
   * Deleted the generated node entities.
   */
  public function deleteContent() {}
}