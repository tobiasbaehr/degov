<?php

namespace Drupal\Tests\media_file_links\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Class ModuleInstallationTest.
 *
 * @package Drupal\Tests\media_file_links\Kernel
 */
class ModuleInstallationTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'media_file_links',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installConfig(['media_file_links']);
  }

  /**
   * Tests that the module is installed and available.
   */
  public function testSetup(): void {
    $moduleHandler = \Drupal::service('module_handler');
    self::assertTrue($moduleHandler->moduleExists('media_file_links'));
    self::assertTrue($moduleHandler->getModule('media_file_links'));
  }

}
