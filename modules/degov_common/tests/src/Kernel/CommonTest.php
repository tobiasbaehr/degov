<?php

namespace Drupal\Tests\degov_common\Kernel;

use Drupal\degov_common\Common;
use Drupal\degov_common\Entity\NodeService;
use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\paragraphs\Entity\ParagraphsType;

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
    'video_embed_field',
		'paragraphs',
		'file',
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

  public function testRemoveNode() {
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

  public function testRemoveParagraph() {
		$paragraph_type = ParagraphsType::create(array(
			'label' => 'test_text',
			'id' => 'test_text',
		));
		$paragraph_type->save();

		$paragraph1 = Paragraph::create([
			'title' => 'Paragraph',
			'type' => 'test_text',
		]);
		$paragraph1->save();

		$paragraph2 = Paragraph::create([
			'title' => 'Paragraph',
			'type' => 'test_text',
		]);
		$paragraph2->save();

		$this->assertEquals(get_class(Paragraph::load($paragraph1->id())), Paragraph::class);
		$this->assertEquals(get_class(Paragraph::load($paragraph2->id())), Paragraph::class);

		$node = Node::create([
			'title' => $this->randomMachineName(),
			'type' => 'article',
			'node_paragraph_field' => array($paragraph1, $paragraph2),
		]);
		$node->save();

		Common::removeContent([
			'entity_type' => 'paragraph',
			'entity_bundles' => ['test_text'],
		]);

		$this->assertEquals(get_class(Paragraph::load($paragraph1->id())), NULL);
		$this->assertEquals(get_class(Paragraph::load($paragraph2->id())), NULL);
	}

}
