<?php

namespace Drupal\Tests\degov_common\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\degov_common\Entity\ConfigAdder;

class ConfigAdderTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'user',
    'system',
    'node',
    'paragraphs',
    'language',
    'degov_common',
    'config_replace',
    'video_embed_field',
    'paragraphs',
    'file',
    'text',
    'taxonomy'
  ];

  /**
   * @var ConfigAdder
   */
  private $configAdder;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installConfig(['taxonomy']);
    $this->configAdder = \Drupal::service('degov_common.config_adder');
  }

  public function testAddListItemFromConfiguration() {
    $originalConfigList = \Drupal::configFactory()
      ->getEditable('core.entity_view_mode.taxonomy_term.full');

    self::assertArrayNotHasKey('saschi', array_flip($originalConfigList->get('dependencies.module')));

    $this->configAdder->addListItemFromConfiguration('core.entity_view_mode.taxonomy_term.full', 'dependencies.module', 'saschi');

    $updatedConfigList = \Drupal::configFactory()
      ->getEditable('core.entity_view_mode.taxonomy_term.full');

    self::assertArrayHasKey('saschi', (!empty($updatedConfigList->get('dependencies.module'))) ? array_flip($updatedConfigList->get('dependencies.module')) : [], 'The dependencies.module config key must contain the "saschi" value in the list.');
  }

}