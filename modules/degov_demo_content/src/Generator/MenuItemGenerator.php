<?php

namespace Drupal\degov_demo_content\Generator;



use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\menu_link_content\Entity\MenuLinkContent;

class MenuItemGenerator extends ContentGenerator implements GeneratorInterface {

  public function __construct(ModuleHandler $moduleHandler, EntityTypeManager $entityTypeManager, Connection $database) {
    parent::__construct($moduleHandler, $entityTypeManager);
    $this->database = $database;
    $this->entityType = 'menu_link_content';
  }

  public function generateContent(): void {

    $definitions = $this->loadDefinitions('menu_item.yml');

    foreach ($definitions as $definition) {

      $firstLevelMenuItem = MenuLinkContent::create([
        'title' => $definition['node_title'],
        'link' => [
          'uri' => 'internal:/node/' . $this->getNidByNodeTitle($definition['node_title']),
        ],
        'menu_name' => 'main',
        'expanded' => TRUE,
      ]);
      $firstLevelMenuItem->save();

      if (!empty($definition['second_level'])) {
        foreach ($definition['second_level'] as $secondLevelDefinitionNodeTitle) {
          $secondLevelMenuItem = MenuLinkContent::create([
            'title' => $secondLevelDefinitionNodeTitle,
            'link' => [
              'uri' => 'internal:/node/' . $this->getNidByNodeTitle($secondLevelDefinitionNodeTitle),
              'attributes' => [
                'class' => [
                  'fa-drupal'
                ]
              ],
            ],
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

  public function deleteContent(): void {
    if ($this->getDemoContentTagId() === NULL) {
      return;
    }
    $entities = \Drupal::entityTypeManager()
      ->getStorage($this->entityType);

    foreach ($entities as $entity) {
      $entity->delete();
    }
  }

  public function resetContent(): void {
    $this->deleteContent();
    $this->generateContent();
  }

}
