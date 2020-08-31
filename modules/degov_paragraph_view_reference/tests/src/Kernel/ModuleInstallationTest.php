<?php

declare(strict_types=1);

namespace Drupal\Tests\degov_paragraph_view_reference\Kernel;

use Drupal\Core\Extension\Extension;
use Drupal\KernelTests\KernelTestBase;

/**
 * Class ModuleInstallationTest
 *
 * @package Drupal\Tests\degov_paragraph_view_reference\Kernel
 */
class ModuleInstallationTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'paragraphs',
    'file',
    'field',
    'link',
    'degov_content_types_shared_fields',
    'viewsreference',
    'media',
    'node',
    'entity_reference_revisions',
    'taxonomy',
    'text',
    'views',
    'system',
    'action',
    'user',
    'image',
    'views_parity_row',
    'lightning_core',
    'entity_reference',
    'link_attributes',
    'degov_paragraph_view_reference',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig(['degov_content_types_shared_fields', 'paragraphs', 'degov_paragraph_view_reference']);
  }

  /**
   * Tests that the module can be installed and is available.
   */
  public function testSetup(): void {
    /** @var \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler */
    $moduleHandler = $this->container->get('module_handler');
    $this->assertInstanceOf(Extension::class, $moduleHandler->getModule('degov_paragraph_view_reference'));
  }

}
