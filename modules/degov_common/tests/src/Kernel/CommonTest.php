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
		list($paragraph1, $paragraph2, $paragraph3) = $this->createParagraphs();

		$idParagraph1 = $paragraph1->id();
		$idParagraph2 = $paragraph2->id();
		$idParagraph3 = $paragraph3->id();

		$this->assertEquals(get_class(Paragraph::load($idParagraph1)), Paragraph::class);
		$this->assertEquals(get_class(Paragraph::load($idParagraph2)), Paragraph::class);
		$this->assertEquals(get_class(Paragraph::load($idParagraph3)), Paragraph::class);

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

		$this->assertEquals(Paragraph::load($idParagraph1), NULL);
		$this->assertEquals(Paragraph::load($idParagraph2), NULL);
		$this->assertEquals(get_class(Paragraph::load($idParagraph3)), Paragraph::class);
	}

	private function createParagraphs(): array
	{
		$paragraph_type = ParagraphsType::create([
			'label' => 'test_text',
			'id'    => 'test_text',
		]);
		$paragraph_type->save();

		$paragraph_type = ParagraphsType::create([
			'label' => 'test_text_not_remove',
			'id'    => 'test_text_not_remove',
		]);
		$paragraph_type->save();

		$paragraph1 = Paragraph::create([
			'title' => 'Paragraph',
			'type'  => 'test_text',
		]);
		$paragraph1->save();

		$paragraph2 = Paragraph::create([
			'title' => 'Paragraph',
			'type'  => 'test_text',
		]);
		$paragraph2->save();

		$paragraph3 = Paragraph::create([
			'title' => 'Paragraph',
			'type'  => 'test_text_not_remove',
		]);
		$paragraph3->save();

		return [
			$paragraph1,
			$paragraph2,
			$paragraph3,
		];
	}

}
