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
   * @command degov_demo_content:reset
   * @aliases dcreg
   */
  public function resetContent() {
    /**
     * @var \Drupal\degov_demo_content\Generator\MediaGenerator $mediaGenerator
     */
    $mediaGenerator = \Drupal::service('degov_demo_content.media_generator');
    $mediaGenerator->resetContent();
    /**
     * @var \Drupal\degov_demo_content\Generator\NodeGenerator $nodeGenerator
     */
    $nodeGenerator = \Drupal::service('degov_demo_content.node_generator');
    $nodeGenerator->resetContent();
    /**
     * @var \Drupal\degov_demo_content\Generator\MenuItemGenerator $menuItemGenerator
     */
    $menuItemGenerator = \Drupal::service('degov_demo_content.menu_item_generator');
    $menuItemGenerator->resetContent();

    $this->logger()->success(dt('Media items & node items & menu items reset.'));
  }

  /**
   * Deletes the demo content.
   *
   * @command degov_demo_content:delete
   * @aliases dcdel
   */
  public function deleteContent() {
    \Drupal::service('degov_demo_content.media_generator')->deleteContent();
    \Drupal::service('degov_demo_content.node_generator')->deleteContent();
    \Drupal::service('degov_demo_content.menu_item_generator')->deleteContent();
  }

  /**
   * Generates the demo content.
   *
   * @command degov_demo_content:generate
   * @aliases dcgen
   */
  public function createContent() {
    \Drupal::service('degov_demo_content.media_generator')->generateContent();
    \Drupal::service('degov_demo_content.node_generator')->generateContent();
    \Drupal::service('degov_demo_content.menu_item_generator')->generateContent();
  }

}
