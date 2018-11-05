<?php

namespace Drupal\degov_demo_content\Command;

use Drupal\degov_demo_content\Generator\MediaGenerator;
use Drupal\degov_demo_content\Generator\MenuItemGenerator;
use Drupal\degov_demo_content\Generator\NodeGenerator;
use Drush\Commands\DrushCommands;

/**
 * Defines drush commands to manage the demo content.
 */
class DegovDemoContentDrushCommands extends DrushCommands {

  /**
   * The deGov Demo Content MediaGenerator.
   *
   * @var \Drupal\degov_demo_content\Generator\MediaGenerator
   */
  private $mediaGenerator;

  /**
   * The deGov Demo Content NodeGenerator.
   *
   * @var \Drupal\degov_demo_content\Generator\NodeGenerator
   */
  private $nodeGenerator;

  /**
   * The deGov Demo Content MenuItemGenerator.
   *
   * @var \Drupal\degov_demo_content\Generator\MenuItemGenerator
   */
  private $menuItemGenerator;

  /**
   * DegovDemoContentDrushCommands constructor.
   *
   * @param \Drupal\degov_demo_content\Generator\MediaGenerator $mediaGenerator
   *   The deGov Demo Content MediaGenerator.
   * @param \Drupal\degov_demo_content\Generator\NodeGenerator $nodeGenerator
   *   The deGov Demo Content NodeGenerator.
   * @param \Drupal\degov_demo_content\Generator\MenuItemGenerator $menuItemGenerator
   *   The deGov Demo Content MenuItemGenerator.
   */
  public function __construct(MediaGenerator $mediaGenerator, NodeGenerator $nodeGenerator, MenuItemGenerator $menuItemGenerator) {
    parent::__construct();
    $this->mediaGenerator = $mediaGenerator;
    $this->nodeGenerator = $nodeGenerator;
    $this->menuItemGenerator = $menuItemGenerator;
  }

  /**
   * Deletes and regenerates the demo content.
   *
   * @command degov_demo_content:reset
   * @aliases dcreg
   */
  public function resetContent() {
    $this->mediaGenerator->resetContent();
    $this->nodeGenerator->resetContent();
    $this->menuItemGenerator->resetContent();

    $this->logger()->success(dt('Media items & node items & menu items reset.'));
  }

  /**
   * Deletes the demo content.
   *
   * @command degov_demo_content:delete
   * @aliases dcdel
   */
  public function deleteContent() {
    $this->menuItemGenerator->deleteContent();
    $this->nodeGenerator->deleteContent();
    $this->mediaGenerator->deleteContent();
  }

  /**
   * Generates the demo content.
   *
   * @command degov_demo_content:generate
   * @aliases dcgen
   */
  public function createContent() {
    $this->mediaGenerator->generateContent();
    $this->nodeGenerator->generateContent();
    $this->menuItemGenerator->generateContent();
  }

}
