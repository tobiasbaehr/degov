<?php

declare(strict_types=1);

namespace Drupal\Tests\degov_rich_text_format_settings\Kernel;

use Drupal\Core\Extension\Extension;
use Drupal\KernelTests\KernelTestBase;

/**
 * Class InstallationTest.
 */
class ModuleInstallationTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'filter',
    'editor',
    'entity_embed',
    'spamspan',
    'ckeditor',
    'linkit',
    'node',
    'degov_rich_text_format_settings'
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig(['degov_rich_text_format_settings']);
  }

  /**
   * Tests that the module can be installed and is available.
   */
  public function testSetup(): void {
    /** @var \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler */
    $moduleHandler = $this->container->get('module_handler');
    $this->assertInstanceOf(Extension::class, $moduleHandler->getModule('degov_rich_text_format_settings'));
  }

}
