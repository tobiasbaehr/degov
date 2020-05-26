<?php

namespace Drupal\Tests\degov_demo_content\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\node\Entity\Node;
use Drupal\Tests\user\Traits\UserCreationTrait;

/**
 * Class MenuItemGeneratorTest.
 */
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
    'link',
  ];

  /**
   * Menu item generator.
   *
   * @var \Drupal\degov_demo_content\Generator\MenuItemGenerator
   */
  private $menuItemGenerator;

  /**
   * Menu link content storage.
   *
   * @var \Drupal\Core\Entity\Sql\SqlContentEntityStorage
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

    $this->menuItemGenerator = $this->container->get('degov_demo_content.menu_item_generator');

    /** @var \Drupal\Core\Entity\EntityTypeManager $entityTypeManager */
    $entityTypeManager = $this->container->get('entity_type.manager');
    $this->menuLinkContentStorage = $entityTypeManager->getStorage('menu_link_content');

    $user = $this->createUser([], NULL, TRUE);
    /** @var \Drupal\Core\Session\AccountProxyInterface $currentUser */
    $currentUser = $this->container->get('current_user');
    $currentUser->setAccount($user);
  }

  /**
   * Menu item generation.
   */
  public function testMenuItemsGeneration(): void {
    $this->generateNodesFromDefinitions();
    $this->menuItemGenerator->generateContent();

    $menuItems = $this->menuLinkContentStorage->loadMultiple();

    $assertedInstances = 0;
    foreach ($menuItems as $menuItem) {
      self::assertInstanceOf(MenuLinkContent::class, $menuItem);
      $assertedInstances++;
    }

    self::assertCount($assertedInstances, $menuItems);
  }

  /**
   * Delete demo menu items only.
   */
  public function testDeleteDemoMenuItemsOnly(): void {
    $this->generateNodesFromDefinitions();
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
      'title' => 'Example.com',
    ]);

    self::assertCount(1, $expectedExistingMenuItems);
    self::assertInstanceOf(MenuLinkContent::class, array_shift($expectedExistingMenuItems));

    $allMenuItems = $this->menuLinkContentStorage->loadMultiple();
    self::assertCount(1, $allMenuItems);
  }

  /**
   * Generate nodes from definitions.
   */
  private function generateNodesFromDefinitions(): void {
    $definitions = $this->menuItemGenerator->loadDefinitions('menu_item.yml');

    $this->generateNodes($definitions);
  }

  /**
   * Generate nodes.
   */
  private function generateNodes(array $definitions): void {
    foreach ($definitions as $definition) {
      $node = Node::create([
        'type'  => 'article',
        'title' => $definition['node_title'],
      ]);
      $node->save();

      if (!empty($definition['children'])) {
        $this->generateNodes($definition['children']);
      }
    }
  }

}
