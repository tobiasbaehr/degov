<?php

namespace Drupal\Tests\degov_devel\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Core\Extension\ModuleHandler;

class ModuleInstallationTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'system',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
  }

  public function testModuleCanBeInstalledAndUninstalled(): void {
    /**
     * @var ModuleHandler $moduleHandler
     */
    $this->container->get('module_installer')->install(['degov_devel']);
    self::assertTrue($this->container->get('module_handler')->moduleExists('degov_devel'));
    self::assertTrue($this->container->get('module_handler')->moduleExists('devel'));
    self::assertTrue($this->container->get('module_handler')->moduleExists('webprofiler'));

    $this->container->get('module_installer')->uninstall(['degov_devel', 'webprofiler', 'devel']);
    self::assertFalse($this->container->get('module_handler')->moduleExists('degov_devel'));
    self::assertFalse($this->container->get('module_handler')->moduleExists('devel'));
    self::assertFalse($this->container->get('module_handler')->moduleExists('webprofiler'));
  }

}