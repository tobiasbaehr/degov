<?php

namespace Drupal\Tests\degov_simplenews\Kernel;

use Drupal\Tests\token\Kernel\KernelTestBase;

class InstallationTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['degov_simplenews_references'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
  }

  public function testInstallation(): void {
    /**
     * @var \Drupal\Core\Extension\ModuleHandler $moduleInstaller
     */
    $moduleInstaller = \Drupal::service('module_handler');
    self::assertTrue($moduleInstaller->moduleExists('degov_simplenews_references'));
  }

}