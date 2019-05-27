<?php

namespace Drupal\degov_demo_content\Generator;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\Core\Menu\MenuLinkInterface;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\menu_link_content\MenuLinkContentInterface;

/**
 * Class MenuItemGenerator.
 *
 * @package Drupal\degov_demo_content\Generator
 */
class MenuItemGenerator extends ContentGenerator implements GeneratorInterface {

  /**
   * @var \Drupal\Core\Database\Connection
   *   The database connection.
   */
  private $database;

  /**
   * MenuItemGenerator constructor.
   *
   * @param \Drupal\Core\Extension\ModuleHandler $moduleHandler
   *   The module handler.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   */
  public function __construct(ModuleHandler $moduleHandler, EntityTypeManagerInterface $entityTypeManager, Connection $database) {
    parent::__construct($moduleHandler, $entityTypeManager);

    $this->database = $database;
    $this->entityType = 'menu_link_content';
  }

  /**
   * Generates menu items from a definitions file.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function generateContent(): void {
    $definitions = $this->loadDefinitions('menu_item.yml');

    $this->generateMenuItems($definitions);
  }

  /**
   * Generates menu items from YAML definitions recursively for menus as deep as we want.
   *
   * @param array $menuItemDefinitions
   * @param \Drupal\Core\Menu\MenuLinkInterface|NULL $parentMenuLink
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  private function generateMenuItems(array $menuItemDefinitions, MenuLinkContentInterface $parentMenuLink = NULL): void {
    foreach ($menuItemDefinitions as $menuItemDefinition) {
      $menuLinkParameters = [
        'title'     => $menuItemDefinition['node_title'],
        'link'      => [
          'uri' => 'internal:/node/' . $this->getNidByNodeTitle($menuItemDefinition['node_title']),
        ],
        'menu_name' => 'main',
        'expanded'  => TRUE,
      ];

      if (!empty($parentMenuLink)) {
        $menuLinkParameters['parent'] = $parentMenuLink->getPluginId();
      }

      if (empty($parentMenuLink) && !empty($definition['fontawesome_css_class'])) {
        $menuLinkParameters['link']['options'] = [
          'attributes' => [
            'class' => [
              $definition['fontawesome_css_class'],
            ],
          ],
        ];
      }

      $menuItem = MenuLinkContent::create($menuLinkParameters);
      $menuItem->save();

      if (!empty($menuItemDefinition['children'])) {
        $this->generateMenuItems($menuItemDefinition['children'], $menuItem);
      }
    }
  }

  /**
   * Gets the ID of a node with the given title.
   *
   * @param string $nodeTitle
   *
   * @return string
   *
   * @throws \Exception
   */
  private function getNidByNodeTitle(string $nodeTitle): string {
    $query = $this->database->select('node_field_data', 'nfd')
      ->fields('nfd', ['nid'])
      ->condition('nfd.title', $nodeTitle);

    $nid = $query->execute()->fetchField();

    if (empty($nid) || !is_numeric($nid)) {
      throw new \Exception(sprintf('No node has been found by node title "%s"', $nodeTitle));
    }

    return $nid;
  }

  /**
   * Deletes the generated content.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function deleteContent(): void {
    $entities = \Drupal::entityTypeManager()
      ->getStorage($this->entityType)
      ->loadMultiple();

    foreach ($entities as $entity) {
      if ($this->isDemoMenuItem($entity)) {
        $entity->delete();
      }
    }
  }

  /**
   * Deletes, then regenerates the demo content.
   */
  public function resetContent(): void {
    $this->deleteContent();
    $this->generateContent();
  }

  /**
   * Checks if the given menu item was generated by this module.
   *
   * @param \Drupal\menu_link_content\Entity\MenuLinkContent $menuLinkItem
   *   The menu item to check.
   *
   * @return bool
   *
   * @throws \Exception
   */
  private function isDemoMenuItem(MenuLinkContent $menuLinkItem): bool {
    $isDemoMenuItem = FALSE;

    $definitions = $this->loadDefinitions('menu_item.yml');

    foreach ($definitions as $definition) {
      if ($menuLinkItem->getTitle() === $definition['node_title']) {
        return TRUE;
      }

      if (!empty($definition['second_level'])) {
        foreach ($definition['second_level'] as $secondLevelDefinitionNodeTitle) {
          if ($menuLinkItem->getTitle() === $secondLevelDefinitionNodeTitle) {
            return TRUE;
          }
        }
      }

    }

    return $isDemoMenuItem;
  }

}
