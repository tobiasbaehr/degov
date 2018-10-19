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
    error_log('content generated ' . $this->state->get($this->state_content_generated_key, FALSE));
    $this->state->set($this->state_content_generated_key, TRUE);
    error_log('content generated ' . $this->state->get($this->state_content_generated_key, FALSE));
  }

  /**
   * Deletes the generated media entities.
   */
  public function deleteMedia() {
    error_log('delete');
    error_log('content generated ' . $this->state->get($this->state_content_generated_key, FALSE));
    $this->state->delete($this->state_content_generated_key);
    error_log('content generated ' . $this->state->get($this->state_content_generated_key, FALSE));
  }

  /**
   * Deletes and re-generates the media entities.
   */
  public function resetMedia() {
    $this->deleteMedia();
    $this->generateMedia();
  }
}
