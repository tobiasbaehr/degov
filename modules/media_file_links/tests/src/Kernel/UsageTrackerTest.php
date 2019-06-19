<?php

namespace Drupal\Tests\media_file_links\Kernel;

use Drupal\menu_link_content\Entity\MenuLinkContent;

/**
 * Class UsageTrackerTest.
 *
 * @package Drupal\Tests\media_file_links\Kernel
 */
class UsageTrackerTest extends MediaFileLinksTestBase {

  private $usageTracker;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->usageTracker = \Drupal::service('media_file_links.usage_tracker');
  }

  public function testUsageRecordIsCreatedFromMenuLink() {
    $usages = $this->usageTracker->getUsagesByMediaIds([$this->supportedMediaId]);
    self::assertEquals([], $usages);

    $this->createMenuItem();

    $usages = $this->usageTracker->getUsagesByMediaIds([$this->supportedMediaId], FALSE);
    self::assertEquals([
      0 => [
        'referencing_entity_id'       => 1,
        'referencing_entity_type'     => 'menu_link_content',
        'referencing_entity_field'    => 'link',
        'referencing_entity_langcode' => 'en',
        'media_entity_id'             => $this->supportedMediaId,
      ],
    ], $usages);
  }

  public function testUsageRecordsAreUpdatedOnContentChange() {
    $usages = $this->usageTracker->getUsagesByMediaIds([$this->supportedMediaId]);
    self::assertEquals([], $usages);

    $menuItemId = $this->createMenuItem();
    $usages = $this->usageTracker->getUsagesByMediaIds([$this->supportedMediaId]);
    self::assertCount(1, $usages);

    $menuItem = MenuLinkContent::load($menuItemId);
    $menuItem->set('link', ['uri' => 'internal:/']);
    $menuItem->save();

    $usages = $this->usageTracker->getUsagesByMediaIds([$this->supportedMediaId]);
    self::assertEquals([], $usages);
  }

  public function testUsageRecordsAreDeletedOnMenuContentDelete() {
    $usages = $this->usageTracker->getUsagesByMediaIds([$this->supportedMediaId]);
    self::assertEquals([], $usages);

    $menuItemId = $this->createMenuItem();
    $usages = $this->usageTracker->getUsagesByMediaIds([$this->supportedMediaId]);
    self::assertCount(1, $usages);

    $this->deleteMenuItem($menuItemId);
    $usages = $this->usageTracker->getUsagesByMediaIds([$this->supportedMediaId]);
    self::assertEquals([], $usages);
  }

  private function createMenuItem() {
    $menuItem = MenuLinkContent::create([
      'title'     => 'Fna',
      'link'      => [
        'uri' => 'internal:<media/file/' . $this->supportedMediaId . '>',
      ],
      'menu_name' => 'main',
      'expanded'  => TRUE,
    ]);
    $menuItem->save();
    return $menuItem->id();
  }

  private function deleteMenuItem(int $itemId) {
    $menuItem = MenuLinkContent::load($itemId);
    $menuItem->delete();
  }

}
