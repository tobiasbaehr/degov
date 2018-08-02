<?php

namespace Drupal\Tests\degov_eu_cookie_compliance\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Core\Extension\ModuleHandler;

class ModuleInstallationTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'degov_eu_cookie_compliance',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installConfig(['degov_eu_cookie_compliance']);
  }

  public function testSetup(): void {
    /**
     * @var ModuleHandler $moduleHandler
     */
    $moduleHandler = \Drupal::service('module_handler');
    self::assertTrue($moduleHandler->moduleExists('degov_eu_cookie_compliance'));
    self::assertTrue($moduleHandler->getModule('degov_eu_cookie_compliance'));
  }

}