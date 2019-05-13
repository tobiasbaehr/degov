<?php

namespace Drupal\Tests\degov_demo_content\Kernel;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\degov_demo_content\Generator\MenuItemGenerator;
use Drupal\KernelTests\KernelTestBase;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\node\Entity\Node;
use Drupal\Tests\user\Traits\UserCreationTrait;


class MenuItemGeneratorTest extends KernelTestBase {

  use UserCreationTrait;

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
   * @var MenuItemGenerator
   */
  private $menuItemGenerator;

  /**
   * @var SqlContentEntityStorage
   */
  private $menuLinkContentStorage;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installEntitySchema('menu_link_content');

    $this->installSchema('system', ['sequences']);

    $this->menuItemGenerator = \Drupal::service('degov_demo_content.menu_item_generator');

    /**
     * @var EntityTypeManager $entityTypeManager
     */
    $entityTypeManager = \Drupal::service('entity_type.manager');
    $this->menuLinkContentStorage = $entityTypeManager->getStorage('menu_link_content');

    $user = $this->createUser([], NULL, TRUE);
    \Drupal::currentUser()->setAccount($user);
  }

  public function testMenuItemsGeneration(): void {
    $this->generateNodes();
    $this->menuItemGenerator->generateContent();

    $menuItems = $this->menuLinkContentStorage->loadMultiple();

    $assertedInstances = 0;
    foreach ($menuItems as $menuItem) {
      self::assertInstanceOf(MenuLinkContent::class, $menuItem);
      $assertedInstances++;
    }

    self::assertCount($assertedInstances, $menuItems);
  }

  public function testDeleteDemoMenuItemsOnly(): void {
    $this->generateNodes();
    $this->menuItemGenerator->generateContent();

    $nonDemoMenuItem = MenuLinkContent::create([
      'title' => 'Example.com',
      'link' => [
        'uri' => 'external:https://example.com',
      ],
      'menu_name' => 'main',
      'expanded' => TRUE,
    ]);
    $nonDemoMenuItem->save();

    $this->menuItemGenerator->deleteContent();

    $expectedExistingMenuItems = $this->menuLinkContentStorage->loadByProperties([
      'title' => 'Example.com'
    ]);

    self::assertCount(1, $expectedExistingMenuItems);
    self::assertInstanceOf(MenuLinkContent::class, array_shift($expectedExistingMenuItems));

    $allMenuItems = $this->menuLinkContentStorage->loadMultiple();
    self::assertCount(1, $allMenuItems);
  }

  private function generateNodes(): void {
    $definitions = $this->menuItemGenerator->loadDefinitions('menu_item.yml');

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
