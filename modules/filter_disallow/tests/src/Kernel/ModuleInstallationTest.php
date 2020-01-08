<?php

namespace Drupal\Tests\filter_disallow\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Class ModuleInstallationTest.
 */
class ModuleInstallationTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'filter_disallow',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installConfig(['filter_disallow']);
  }

  /**
   * Test setup.
   */
  public function testSetup(): void {
    /**
     * @var \Drupal\Core\Extension\ModuleHandler $moduleHandler
     */
    $moduleHandler = \Drupal::service('module_handler');
    self::assertTrue($moduleHandler->moduleExists('filter_disallow'));
    self::assertTrue($moduleHandler->getModule('filter_disallow'));
  }

}
