<?php

namespace Drupal\Tests\degov_demo_content\Kernel;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\degov_demo_content\Generator\ContentGenerator;
use Drupal\degov_demo_content\Generator\MenuItemGenerator;
use Drupal\KernelTests\KernelTestBase;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\node\Entity\Node;


class MenuItemGeneratorTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'system',
    'degov_demo_content',
    'node',
    'pathauto',
    'geofield',
    'token',
    'user',
    'menu_link_content',
    'link'
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installEntitySchema('menu_link_content');
  }

  public function testMenuItemsGeneration(): void {
    $this->generateNodes();

    /**
     * @var MenuItemGenerator $menuItemGenerator
     */
    $menuItemGenerator = \Drupal::service('degov_demo_content.menu_item_generator');
    $menuItemGenerator->generateContent();

    /**
     * @var EntityTypeManager $entityTypeManager
     */
    $entityTypeManager = \Drupal::service('entity_type.manager');
    $menuItems = $entityTypeManager->getStorage('menu_link_content')->loadMultiple();

    $assertedInstanced = 0;
    foreach ($menuItems as $menuItem) {
      self::assertInstanceOf(MenuLinkContent::class, $menuItem);
      $assertedInstanced++;
    }

    self::assertCount($assertedInstanced, $menuItems);
  }

  private function generateNodes() {
    /**
     * @var ContentGenerator $contentGenerator
     */
    $contentGenerator = \Drupal::service('degov_demo_content.content_generator');

    $definitions = $contentGenerator->loadDefinitions('menu_item.yml');

    foreach ($definitions as $definition) {
      $node = Node::create([
        'type'  => 'article',
        'title' => $definition['node_title'],
      ]);
      $node->save();

      if (!empty($definition['second_level'])) {
        foreach ($definition['second_level'] as $secondLevelDefinitionNodeTitle) {
          $node = Node::create([
            'type' => 'article',
            'title' => $secondLevelDefinitionNodeTitle,
          ]);
          $node->save();
        }
      }

    }
  }

}
