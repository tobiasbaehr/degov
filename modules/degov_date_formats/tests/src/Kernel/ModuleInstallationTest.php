<?php

namespace Drupal\Tests\degov_date_formats\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Core\Extension\ModuleHandler;

class ModuleInstallationTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'degov_date_formats',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installConfig(['degov_date_formats']);
  }

  public function testSetup(): void {
    /**
     * @var ModuleHandler $moduleHandler
     */
    $moduleHandler = \Drupal::service('module_handler');
    self::assertTrue($moduleHandler->moduleExists('degov_date_formats'));
    self::assertTrue($moduleHandler->getModule('degov_date_formats'));
  }

}
