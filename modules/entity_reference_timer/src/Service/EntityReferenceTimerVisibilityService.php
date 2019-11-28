<?php

namespace Drupal\entity_reference_timer\Service;

use Drupal\Core\Database\Connection;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\entity_reference_timer\Plugin\Field\FieldType\EntityReferenceDate;
use Drupal\node\NodeInterface;

class EntityReferenceTimerVisibilityService {

  /**
   * @var \Drupal\Core\Database\Connection
   */
  private $database;

  public function __construct(Connection $database) {
    $this->database = $database;
  }

  public function getNextExpirationTimestampFromItem(EntityReferenceDate $item): ?int {
    $now = time();
    $closestTimestamp = NULL;
    foreach (['start_date', 'end_date'] as $fieldName) {
      $dateTime = $item->get($fieldName)->getValue();
      if (\is_string($dateTime)) {
        $dateTime = new DrupalDateTime($dateTime);
      }
      if ($dateTime instanceof DrupalDateTime && $dateTime->getTimestamp() > $now && ($closestTimestamp === NULL || $dateTime->getTimestamp() < $closestTimestamp)) {
        $closestTimestamp = $dateTime->getTimestamp();
      }
    }
    return $closestTimestamp;
  }

  public function addExpirationDateToParentNode(FieldItemInterface $item): void {
    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node instanceof NodeInterface) {
      $cacheTagString = 'node:' . $node->id();

      $cacheTagResult = $this->database->select('cachetags', 'ct')
        ->fields('ct')
        ->range(0, 1)
        ->condition('tag', $cacheTagString)
        ->execute();
      $cacheTag = $cacheTagResult->fetchAll();
      $nextExpirationTime = $this->getNextExpirationTimestampFromItem($item);

      if ($nextExpirationTime !== NULL) {
        if (!empty($cacheTag[0]) && property_exists($cacheTag[0], 'expires')) {
          if ($cacheTag[0]->expires === NULL || $cacheTag[0]->expires > $nextExpirationTime) {
            $this->database->update('cachetags')
              ->condition('tag', $cacheTagString)
              ->fields(['expires' => $nextExpirationTime])
              ->execute();
          }
        } else {
          $this->database->insert('cachetags')
            ->fields([
              'tag'           => $cacheTagString,
              'invalidations' => 0,
              'expires'       => $nextExpirationTime,
            ])
            ->execute();
        }
      }
    }
  }

  public function countVisibleItemsInArray(array $items): int {
    $visibleItems = 0;
    foreach ($items as $item) {
      if ($this->isVisibleArray($item)) {
        $visibleItems++;
      }
    }
    return $visibleItems;
  }

  public function isVisible(FieldItemInterface $item): bool {
    if ($this->referenceItemSupportsTimedDisplay($item)) {
      return $this->isDateInThe('past', $item->get('start_date')
        ->getValue()) && $this->isDateInThe('future', $item->get('end_date')
        ->getValue());
    }

    return TRUE;
  }

  public function isVisibleArray(array $item): bool {
    if ($this->timedDisplayFieldsPresent($item)) {
      return $this->isDateInThe('past', $item['start_date']) && $this->isDateInThe('future', $item['end_date']);
    }

    return TRUE;
  }

  private function isDateInThe(string $era, $value): bool {
    if (empty($value)) {
      return TRUE;
    }

    $dtNow = new DrupalDateTime();
    $dtFromValue = new DrupalDateTime($value);
    switch ($era) {
      case 'future':
        return $dtNow < $dtFromValue;

      case 'past':
        return $dtNow >= $dtFromValue;

    }
  }

  public function referenceItemSupportsTimedDisplay(FieldItemInterface $item): bool {
    $properties = $item->getProperties(TRUE);
    return $this->timedDisplayFieldsPresent($properties);
  }

  private function timedDisplayFieldsPresent($fields): bool {
    return \array_key_exists('start_date', $fields) && \array_key_exists('end_date', $fields);
  }

}
