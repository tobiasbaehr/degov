<?php

namespace Drupal\Tests\degov_common\Kernel;

use Drupal\degov_common\Common;
use Drupal\degov_common\Entity\NodeService;
use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;

class CommonTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'user',
    'system',
    'node',
    'paragraphs',
    'degov_common',
    'config_rewrite',
    'video_embed_field'
  ];

  /**
   * @var NodeService
   */
  private $nodeService;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('user');
    $this->installEntitySchema('paragraph');
    $this->installSchema('system', ['sequences']);
    $this->installEntitySchema('node');
    $this->installSchema('node', 'node_access');
    \Drupal::moduleHandler()->loadInclude('paragraphs', 'install');
    $this->nodeService = \Drupal::service('degov_common.node');
  }

  public function testRemoveContent() {
    $node = Node::create([
      'title' => 'An article node',
      'type' => 'article',
    ]);
    $node->save();

    $nodeLoaded = $this->nodeService->load([
      'title' => 'An article node'
    ]);
    $this->assertEquals(get_class($nodeLoaded), Node::class);

    Common::removeContent([
      'entity_type' => 'node',
      'entity_bundles' => ['article'],
    ]);
    $nodeLoaded = $this->nodeService->load([
      'title' => 'An article node'
    ]);
    $this->assertEquals($nodeLoaded, NULL);

  }

}
