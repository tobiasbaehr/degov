<?php

namespace Drupal\degov_demo_content\Service;

use Drupal\Core\State\StateInterface;

/**
 * Class ContentGenerator.
 */
class ContentGenerator {

  /**
   * The Key/Value Store to use for state.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * The key of our State variable
   *
   * @var string
   */
  private $state_content_generated_key = 'degov_demo_content.content_generated';

  /**
   * Constructs a new ContentGenerator object.
   */
  public function __construct($state) {
    $this->state = $state;
  }

  /**
   * Generates a set of media entities.
   */
  public function generateMedia() {
    error_log('generate');
    error_log('content generated ' . $this->getGeneratedStatus());
    $this->state->set($this->state_content_generated_key, TRUE);
    error_log('content generated ' . $this->getGeneratedStatus());
  }

  /**
   * Deletes the generated media entities.
   */
  public function deleteMedia() {
    error_log('delete');
    error_log('content generated ' . $this->getGeneratedStatus());
    $this->state->delete($this->state_content_generated_key);
    error_log('content generated ' . $this->getGeneratedStatus());
  }

  /**
   * Deletes and re-generates the media entities.
   */
  public function resetMedia() {
    $this->deleteMedia();
    $this->generateMedia();
  }

  /**
   * Returns the current value of the generated-status State variable
   */
  public function getGeneratedStatus() {
    return $this->state->get($this->state_content_generated_key, FALSE);
  }
}
