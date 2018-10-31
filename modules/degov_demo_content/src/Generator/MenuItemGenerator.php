<?php

namespace Drupal\degov_demo_content\Generator;



use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\menu_link_content\Entity\MenuLinkContent;

class MenuItemGenerator extends ContentGenerator {

  public function __construct(ModuleHandler $moduleHandler, EntityTypeManager $entityTypeManager, Connection $database) {
    parent::__construct($moduleHandler, $entityTypeManager);
    $this->database = $database;
  }

  public function generateMenuItems(): void {

    $definitions = $this->loadDefinitions('menu_item.yml');

    foreach ($definitions as $definition) {

      $firstLevelMenuItem = MenuLinkContent::create([
        'title' => $definition['node_title'],
        'link' => ['uri' => 'internal:/node/' . $this->getNidByNodeTitle($definition['node_title'])],
        'menu_name' => 'main',
        'expanded' => TRUE,
      ]);
      $firstLevelMenuItem->save();

      if (!empty($definition['second_level'])) {
        foreach ($definition['second_level'] as $secondLevelDefinitionNodeTitle) {
          $secondLevelMenuItem = MenuLinkContent::create([
            'title' => $secondLevelDefinitionNodeTitle,
            'link' => ['uri' => 'internal:/node/' . $this->getNidByNodeTitle($secondLevelDefinitionNodeTitle)],
            'parent' => $firstLevelMenuItem->getPluginId(),
            'expanded' => TRUE,
          ]);
          $secondLevelMenuItem->save();
        }
      }

    }

  }

  private function getNidByNodeTitle(string $nodeTitle): string {
    $query = $this->database->select('node_field_data', 'nfd')
      ->fields('nfd', ['nid'])
      ->condition('nfd.title', $nodeTitle);

    $nid = $query->execute()->fetchField();

    if (empty($nid) || !is_numeric($nid)) {
      throw new \Exception('No node has been found by node title.');
    }

    return $nid;
  }

}
