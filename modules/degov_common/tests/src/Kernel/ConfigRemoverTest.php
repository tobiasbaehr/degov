<?php

namespace Drupal\Tests\degov_common\Kernel;

use Drupal\Tests\token\Kernel\KernelTestBase;
use Drupal\degov_common\Entity\ConfigRemover;

class ConfigRemoverTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'user',
    'system',
    'node',
    'paragraphs',
    'degov_common',
    'config_replace',
    'video_embed_field',
    'paragraphs',
    'file',
    'text',
    'taxonomy'
  ];

  /**
   * @var ConfigRemover
   */
  private $configRemover;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('user');
    $this->installEntitySchema('paragraph');
    $this->installEntitySchema('taxonomy_term');
    $this->installSchema('system', ['sequences']);
    $this->installEntitySchema('node');
    $this->installSchema('node', 'node_access');
    \Drupal::moduleHandler()->loadInclude('paragraphs', 'install');
    \Drupal::moduleHandler()->loadInclude('taxonomy', 'install');
    $this->configRemover = \Drupal::service('degov_common.config_remover');
  }

  public function testConfigRemove() {
    $this->configRemover->


  }

}