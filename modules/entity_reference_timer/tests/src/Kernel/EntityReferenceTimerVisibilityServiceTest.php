<?php

namespace Drupal\Tests\entity_reference_timer\Unit;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\TypedData\TypedDataInterface;
use Drupal\entity_reference_timer\Service\EntityReferenceTimerVisibilityService;
use Drupal\KernelTests\KernelTestBase;

/**
 * Class EntityReferenceTimerVisibilityServiceTest.
 *
 * @package Drupal\Tests\entity_reference_timer\Unit
 */
class EntityReferenceTimerVisibilityServiceTest extends KernelTestBase {

  /**
   * Service.
   *
   * @var \Drupal\entity_reference_timer\Service\EntityReferenceTimerVisibilityService
   */
  private $service;

  /**
   * Basic item.
   *
   * @var \PHPUnit\Framework\MockObject\MockObject
   */
  private $basicItem;

  /**
   * The future.
   *
   * @var \PHPUnit\Framework\MockObject\MockObject
   */
  private $theFuture;

  /**
   * The past.
   *
   * @var \PHPUnit\Framework\MockObject\MockObject
   */
  private $thePast;

  /**
   * Now.
   *
   * @var \PHPUnit\Framework\MockObject\MockObject
   */
  private $now;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->service = new EntityReferenceTimerVisibilityService(\Drupal::database());

    $this->theFuture = $this->getMockBuilder(TypedDataInterface::class)
      ->disableOriginalConstructor()
      ->getMock();
    $this->theFuture->method('getValue')
      ->willReturn(strftime('%F %X %Z', strtotime('tomorrow')));
    $this->thePast = $this->getMockBuilder(TypedDataInterface::class)
      ->disableOriginalConstructor()
      ->getMock();
    $this->thePast->method('getValue')
      ->willReturn(strftime('%F %X %Z', strtotime('yesterday')));
    $this->now = $this->getMockBuilder(TypedDataInterface::class)
      ->disableOriginalConstructor()
      ->getMock();
    $this->now->method('getValue')
      ->willReturn(strftime('%F %X %Z', strtotime('now')));

    $this->basicItem = $this
      ->getMockBuilder(FieldItemBase::class)
      ->disableOriginalConstructor()
      ->getMock();
    $this->basicItem
      ->method('getProperties')
      ->will($this->returnValue([
        'start_date' => 'start_date',
        'end_date'   => 'end_date',
      ]));
  }

  /**
   * Test item is not visible if before timeframe.
   */
  public function testItemIsNotVisibleIfBeforeTimeframe(): void {
    $itemInTheFuture = $this->basicItem;
    $itemInTheFuture
      ->method('get')
      ->with($this->logicalOr('start_date', 'end_date'))
      ->will($this->returnCallback([$this, 'getDatetimesForFutureItem']));
    $this->assertFalse($this->service->isVisible($itemInTheFuture));
  }

  /**
   * Test item is not visible if after timeframe.
   */
  public function testItemIsNotVisibleIfAfterTimeframe(): void {
    $itemInThePast = $this->basicItem;
    $itemInThePast
      ->method('get')
      ->with($this->logicalOr('start_date', 'end_date'))
      ->will($this->returnCallback([$this, 'getDatetimesForPastItem']));
    $this->assertFalse($this->service->isVisible($itemInThePast));
  }

  /**
   * Test items are visible if within timeframe.
   */
  public function testItemsAreVisibleIfWithinTimeframe(): void {
    $currentItem = $this->basicItem;
    $currentItem
      ->method('get')
      ->with($this->logicalOr('start_date', 'end_date'))
      ->will($this->returnCallback([$this, 'getDatetimesForCurrentItem']));
    $this->assertTrue($this->service->isVisible($currentItem));

    $currentItemWithoutEndDate = $this->basicItem;
    $currentItemWithoutEndDate
      ->method('get')
      ->with($this->logicalOr('start_date', 'end_date'))
      ->will($this->returnCallback([
        $this,
        'getDatetimesForCurrentItemWithoutEndDate',
      ]));
    $this->assertTrue($this->service->isVisible($currentItemWithoutEndDate));
  }

  /**
   * Return a start date in the future. End date does not matter here.
   *
   * @param string $field
   *   Field.
   *
   * @return \Drupal\Core\TypedData\TypedDataInterface
   *   Typed data interface.
   */
  public function getDatetimesForFutureItem(string $field): TypedDataInterface {
    return $this->theFuture;
  }

  /**
   * Return an end date in the past. Start date does not matter here.
   *
   * @param string $field
   *   Field.
   *
   * @return \Drupal\Core\TypedData\TypedDataInterface
   *   Typed data interface.
   */
  public function getDatetimesForPastItem(string $field): TypedDataInterface {
    return $this->thePast;
  }

  /**
   * Return a start date in the past and an end date in the future.
   *
   * @param string $field
   *   Field.
   *
   * @return \Drupal\Core\TypedData\TypedDataInterface
   *   Typed data interface.
   */
  public function getDatetimesForCurrentItem(string $field): TypedDataInterface {
    switch ($field) {
      case 'start_date':
        return $this->thePast;

      case 'end_date':
        return $this->theFuture;

    }
  }

  /**
   * Only return a start date in the past, return null for the empty end date.
   *
   * @param string $field
   *   Field.
   *
   * @return \Drupal\Core\TypedData\TypedDataInterface|null
   *   Typed data interface.
   */
  public function getDatetimesForCurrentItemWithoutEndDate(string $field): ?TypedDataInterface {
    if ($field === 'start_date') {
      return $this->thePast;
    }
    return NULL;
  }

}