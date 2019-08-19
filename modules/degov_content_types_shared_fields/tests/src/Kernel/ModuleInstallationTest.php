<?php

namespace Drupal\Tests\degov_content_types_shared_fields\Kernel;

use Drupal\Core\Extension\ModuleHandler;
use Drupal\Tests\field\Kernel\FieldKernelTestBase;

class ModuleInstallationTest extends FieldKernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'degov_content_types_shared_fields',
    'media',
    'image',
    'node',
    'lightning_core',
    'link',
    'paragraphs',
    'entity_reference_revisions',
    'taxonomy',
    'views',
    'views_parity_row'
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('media');
    $this->installEntitySchema('node');
    $this->installConfig(['degov_content_types_shared_fields']);
  }

  public function testSetup(): void {
    /**
     * @var ModuleHandler $moduleHandler
     */
    $moduleHandler = \Drupal::service('module_handler');
    self::assertTrue($moduleHandler->moduleExists('degov_content_types_shared_fields'));
    self::assertTrue($moduleHandler->getModule('degov_content_types_shared_fields'));
  }

}
