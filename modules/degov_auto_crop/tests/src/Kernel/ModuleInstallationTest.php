<?php

namespace Drupal\Tests\degov_auto_crop\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Class ModuleInstallationTest.
 *
 * @package Drupal\Tests\degov_breadcrumb\Kernel
 */
class ModuleInstallationTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'degov_auto_crop',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installConfig(['degov_auto_crop']);
  }

  /**
   * Tests that the module is installed and available.
   */
  public function testSetup(): void {
    $moduleHandler = \Drupal::service('module_handler');
    self::assertTrue($moduleHandler->moduleExists('degov_auto_crop'));
    self::assertTrue($moduleHandler->getModule('degov_auto_crop'));
  }

}
