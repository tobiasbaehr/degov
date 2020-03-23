<?php

namespace Drupal\degov_demo_content\Generator;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\degov_demo_content\FileHandler\ParagraphsFileHandler;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\menu_link_content\MenuLinkContentInterface;

/**
 * Class MenuItemGenerator.
 *
 * @package Drupal\degov_demo_content\Generator
 */
class MenuItemGenerator extends ContentGenerator implements GeneratorInterface {

  /**
   * Database.
   *
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
   * @param \Drupal\degov_demo_content\FileHandler\ParagraphsFileHandler $paragraphsFileHandler
   *   Paragraphs file handler.
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   */
  public function __construct(ModuleHandler $moduleHandler, EntityTypeManagerInterface $entityTypeManager, ParagraphsFileHandler $paragraphsFileHandler, Connection $database) {
    parent::__construct($moduleHandler, $entityTypeManager, $paragraphsFileHandler);

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
   * Generate menu items.
   *
   * Generates menu items from YAML definitions recursively for menus as
   * deep as we want.
   *
   * @param array $menuItemDefinitions
   *   Menu item definitions.
   * @param \Drupal\menu_link_content\MenuLinkContentInterface|NULL $parentMenuLink
   *   Parent menu link.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  private function generateMenuItems(array $menuItemDefinitions, MenuLinkContentInterface $parentMenuLink = NULL): void {
    foreach ($menuItemDefinitions as $key => $menuItemDefinition) {
      $title = $menuItemDefinition['menu_title'] ?? $menuItemDefinition['node_title'];
      $menuLinkParameters = [
        'title'     => $title,
        'link'      => [
          'uri' => 'internal:/node/' . $this->getNidByNodeTitle($menuItemDefinition['node_title']),
        ],
        'menu_name' => $menuItemDefinition['menu_name'],
        'expanded'  => TRUE,
      ];

      if (isset($menuItemDefinition['menu_weight'])) {
        $menuLinkParameters['weight'] = $menuItemDefinition['menu_weight'];
      }

      if (!empty($parentMenuLink)) {
        $menuLinkParameters['parent'] = $parentMenuLink->getPluginId();
      }

      if (empty($parentMenuLink) && !empty($menuItemDefinition['fontawesome_css_class'])) {
        $menuLinkParameters['link']['options'] = [
          'attributes' => [
            'class' => [
              $menuItemDefinition['fontawesome_css_class'],
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
   *   Node title.
   *
   * @return string
   *   Node ID.
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
   *   Demo menu item.
   *
   * @throws \Exception
   */
  private function isDemoMenuItem(MenuLinkContent $menuLinkItem): bool {
    $definitions = $this->loadDefinitions('menu_item.yml');
    return \in_array($menuLinkItem->getTitle(), $this->getMenuTitlesFromDefinition($definitions), TRUE);
  }

  /**
   * Get menu titles from definition.
   *
   * Recursively checks if a menu item title is contained in the
   * definitions array.
   *
   * @param array $definitions
   *   Definitions.
   *
   * @return array
   *   Menu titles.
   */
  private function getMenuTitlesFromDefinition(array $definitions): array {
    $titlesArray = [];

    foreach ($definitions as $definition) {
      $titlesArray[] = $definition['menu_title'] ?? $definition['node_title'];

      if (!empty($definition['children'])) {
        $titlesArray = array_merge($titlesArray, $this->getMenuTitlesFromDefinition($definition['children']));
      }
    }

    return $titlesArray;
  }

}
