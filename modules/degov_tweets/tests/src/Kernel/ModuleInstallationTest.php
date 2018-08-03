<?php

namespace Drupal\Tests\degov_tweets\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Core\Extension\ModuleHandler;

class ModuleInstallationTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'degov_tweets',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installConfig(['degov_tweets']);
  }

  public function testSetup(): void {
    /**
     * @var ModuleHandler $moduleHandler
     */
    $moduleHandler = \Drupal::service('module_handler');
    self::assertTrue($moduleHandler->moduleExists('degov_tweets'));
    self::assertTrue($moduleHandler->getModule('degov_tweets'));
  }

}