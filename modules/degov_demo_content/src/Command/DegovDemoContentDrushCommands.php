<?php

namespace Drupal\degov_demo_content\Command;

use Drush\Commands\DrushCommands;

/**
 * Defines drush commands to manage the demo content.
 */
class DegovDemoContentDrushCommands extends DrushCommands {

  /**
   * Deletes and regenerates the demo content.
   *
   * @option option-name
   *   Description
   * @usage degov_demo_content-commandName foo
   *   Usage description
   *
   * @command degov_demo_content:reset
   * @aliases dcreg
   */
  public function resetContent() {
    \Drupal::service('degov_demo_content.media_generator')->resetContent();
    \Drupal::service('degov_demo_content.node_generator')->resetContent();
    $this->logger()->success(dt('Media & Node reset.'));
  }

  /**
   * Deletes the demo content.
   *
   * @option option-name
   *   Description
   * @usage degov_demo_content-commandName foo
   *   Usage description
   *
   * @command degov_demo_content:delete
   * @aliases dcreg
   */
  public function deleteContent() {
    \Drupal::service('degov_demo_content.media_generator')->deleteContent();
    \Drupal::service('degov_demo_content.node_generator')->deleteContent();
  }

  /**
   * Generates the demo content.
   *
   * @option option-name
   *   Description
   * @usage degov_demo_content-commandName foo
   *   Usage description
   *
   * @command degov_demo_content:generate
   * @aliases dcreg
   */
  public function createContent() {
    \Drupal::service('degov_demo_content.media_generator')->generateContent();
    \Drupal::service('degov_demo_content.node_generator')->generateContent();
  }
}
