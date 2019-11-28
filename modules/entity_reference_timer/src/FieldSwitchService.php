<?php

namespace Drupal\entity_reference_timer;

use Drupal\Core\Entity\Display\EntityDisplayInterface;
use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;


class FieldSwitchService {

  /**
   * @var string
   */
  public static $fieldName = 'field_node_reference_nodes';

  /**
   * @var string
   */
  public static $fieldType = 'entity_reference_date';

  /**
   * @var string
   */
  public static $entityType = 'paragraph';

  /**
   * @var string
   */
  public static $bundle = 'node_reference';

  /**
   * @var string
   */
  private static $display = 'entity_reference_date_display_default';

  public static function updateField(): void {
    self::switchFieldStorageAndMigrateContent();
    self::switchFieldWidget();
    self::switchFormDisplay();
    self::switchFieldDisplay();
  }

  private static function switchFieldDisplay(): void {
    /**
     * @var EntityViewDisplayInterface $display
     */
    $display = EntityViewDisplay::load(self::$entityType . '.' . self::$bundle . '.' . 'default');

    $display->setComponent(self::$fieldName, [
      'weight'               => 3,
      'label'                => 'hidden',
      'third_party_settings' => [],
      'type'                 => self::$display,
      'region'               => 'content',
    ])->save();
  }

  public static function uninstallField(): void {
    self::$fieldType = 'entity_reference';
    self::$display = 'entity_reference_display_default';
    self::updateField();
  }

  private static function switchFormDisplay(): void {
    /**
     * @var EntityDisplayInterface $formDisplay
     */
    $formDisplay = \Drupal::entityTypeManager()
       ->getStorage('entity_form_display')
       ->load(self::$entityType . '.' . self::$bundle . '.' . 'default');

    $formDisplay->setComponent(self::$fieldName, ['weight' => 10])->save();
  }

  private static function switchFieldWidget(): void {
    $fields = \Drupal::entityTypeManager()->getStorage('field_config')
      ->loadByProperties(['field_name' => self::$fieldName]);

    $field = array_shift($fields);

    $newField = $field->toArray();
    $newField['field_type'] = self::$fieldType;

    $newField = FieldConfig::create($newField);
    $newField->original = $field;
    $newField->enforceIsNew(FALSE);
    $newField->save();
  }

  private static function switchFieldStorageAndMigrateContent(): void {
    $database = \Drupal::database();
    $table = 'paragraph__field_node_reference_nodes';

    $fieldStorage = FieldStorageConfig::loadByName(self::$entityType, self::$fieldName);

    if ($fieldStorage === NULL) {
      return;
    }

    $rows = NULL;

    if ($database->schema()->tableExists($table)) {
      $rows = $database->select($table, 'p')
        ->fields('p')
        ->execute()
        ->fetchAll();
    }

    $newFields = [];

    foreach ($fieldStorage->getBundles() as $bundle => $label) {
      $field = FieldConfig::loadByName(self::$entityType, $bundle, self::$fieldName);
      $newField = $field->toArray();
      $newField['type'] = self::$fieldType;
      $newFields[] = $newField;
    }

    $newFieldStorage = $fieldStorage->toArray();
    $newFieldStorage['type'] = self::$fieldType;

    $fieldStorage->delete();

    field_purge_batch(10);

    $newFieldStorage = FieldStorageConfig::create($newFieldStorage);
    $newFieldStorage->save();

    foreach ($newFields as $newField) {
      $newField = FieldConfig::create($newField);
      $newField->save();
    }

    if ($rows !== NULL) {
      foreach ($rows as $row) {

        if (self::$fieldType === 'entity_reference') {
          unset($row->field_node_reference_nodes_start_date, $row->field_node_reference_nodes_end_date);
        }

        $database->insert($table)
          ->fields((array) $row)
          ->execute();
      }
    }
  }

}