<?php

declare(strict_types=1);

namespace Drupal\Tests\degov_paragraph_block_reference\Kernel;

use Drupal\Core\Extension\Extension;
use Drupal\KernelTests\KernelTestBase;

/**
 * Class ModuleInstallationTest
 *
 * @package Drupal\Tests\degov_paragraph_block_reference\Kernel
 */
class ModuleInstallationTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'user',
    'system',
    'field',
    'text',
    'block_field',
    'paragraphs',
    'file',
    'link',
    'media',
    'node',
    'taxonomy',
    'views',
    'image',
    'views_parity_row',
    'lightning_core',
    'entity_reference_revisions',
    'degov_content_types_shared_fields',
    'degov_paragraph_block_reference',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig(static::$modules);
  }

  /**
   * Test setup.
   */
  public function testSetup(): void {
    /**
     * @var \Drupal\Core\Extension\ModuleHandler $moduleHandler
     */
    $moduleHandler = $this->container->get('module_handler');
    self::assertInstanceOf(Extension::class, $moduleHandler->getModule('degov_paragraph_block_reference'));
  }

}
