<?php

namespace Drupal\Tests\degov_simplenews_references\Kernel;

use Drupal\Tests\token\Kernel\KernelTestBase;

/**
 * Class InstallationTest.
 */
class InstallationTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['degov_simplenews_references'];

  /**
   * Installation.
   */
  public function testInstallation(): void {
    /**
     * @var \Drupal\Core\Extension\ModuleHandler $moduleInstaller
     */
    $moduleInstaller = \Drupal::service('module_handler');
    self::assertTrue($moduleInstaller->moduleExists('degov_simplenews_references'));
  }

}
