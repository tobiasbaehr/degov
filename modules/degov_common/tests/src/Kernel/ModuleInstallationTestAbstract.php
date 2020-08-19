<?php

declare(strict_types=1);

namespace Drupal\Tests\degov_common\Kernel;

use Drupal\Core\Extension\Extension;
use Drupal\KernelTests\KernelTestBase;

/**
 * Class ModuleInstallationTestAbstract.
 */
abstract class ModuleInstallationTestAbstract extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $class = get_called_class();
    $this->installConfig($class::$modules);
  }

  /**
   * Tests that the module can be installed and is available.
   */
  public function testSetup(): void {
    /** @var \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler */
    $moduleHandler = $this->container->get('module_handler');
    $class = get_called_class();
    $this->assertInstanceOf(Extension::class, $moduleHandler->getModule(reset($class::$modules)));
  }

}
