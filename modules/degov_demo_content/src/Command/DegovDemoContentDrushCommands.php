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
    \Drupal::service('degov_demo_content.content_generator')->resetContent();
    $this->logger()->success(dt('Media reset.'));
  }
}
