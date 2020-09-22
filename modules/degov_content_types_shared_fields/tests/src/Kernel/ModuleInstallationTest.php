<?php

namespace Drupal\Tests\degov_content_types_shared_fields\Kernel;

use Drupal\Core\Extension\Extension;
use Drupal\KernelTests\KernelTestBase;

/**
 * Class ModuleInstallationTest.
 */
class ModuleInstallationTest extends KernelTestBase {
  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'field',
    'media',
    'node',
    'system',
    'text',
    'link',
    'paragraphs',
    'entity_reference_revisions',
    'taxonomy',
    'views',
    'user',
    'image',
    'lightning_core',
    'views_parity_row',
    'degov_content_types_shared_fields',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installConfig(static::$modules);
  }

  /**
   * Tests that the module can be installed and is available.
   */
  public function testSetup(): void {
    /** @var \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler */
    $moduleHandler = $this->container->get('module_handler');
    self::assertInstanceOf(Extension::class, $moduleHandler->getModule('degov_content_types_shared_fields'));
  }

}
