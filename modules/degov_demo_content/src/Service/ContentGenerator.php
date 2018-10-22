<?php

namespace Drupal\degov_demo_content\Service;

use Drupal\degov_demo_content\Factory\MediaFactory;
use Drupal\degov_demo_content\Factory\NodeFactory;

/**
 * Class ContentGenerator.
 */
class ContentGenerator {

  /**
   * Factory class to generate media entities.
   *
   * @var \Drupal\degov_demo_content\Factory\MediaFactory
   */
  private $mediaFactory;

  /**
   * Factory class to generate node entities.
   *
   * @var \Drupal\degov_demo_content\Factory\NodeFactory;
   */
  private $nodeFactory;

  /**
   * Constructs a new ContentGenerator object.
   */
  public function __construct() {
    $this->mediaFactory = new MediaFactory();
    $this->nodeFactory = new NodeFactory();
  }

  /**
   * Generates a set of media entities.
   */
  public function generateContent() {
    $this->mediaFactory->generateContent();
    $this->nodeFactory->generateContent();
  }

  /**
   * Deletes the generated media entities.
   */
  public function deleteContent() {
    $this->nodeFactory->deleteContent();
    $this->mediaFactory->deleteContent();
  }

  /**
   * Deletes and re-generates the media entities.
   */
  public function resetContent() {
    $this->deleteContent();
    $this->generateContent();
  }
}
