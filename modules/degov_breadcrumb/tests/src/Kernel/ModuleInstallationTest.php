<?php

namespace Drupal\Tests\degov_breadcrumb\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Class ModuleInstallationTest.
 */
class ModuleInstallationTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'degov_breadcrumb',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installConfig(['degov_breadcrumb']);
  }

  /**
   * Test setup.
   */
  public function testSetup(): void {
    /**
     * @var \Drupal\Core\Extension\ModuleHandler $moduleHandler
     */
    $moduleHandler = \Drupal::service('module_handler');
    self::assertTrue($moduleHandler->moduleExists('degov_breadcrumb'));
    self::assertTrue($moduleHandler->getModule('degov_breadcrumb'));
  }

}
