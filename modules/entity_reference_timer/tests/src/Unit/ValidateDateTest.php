<?php

namespace Drupal\Tests\entity_reference_timer\Unit;

use Drupal\entity_reference_timer\InputValidator;
use Drupal\Tests\UnitTestCase;


/**
 * Tests the text formatters functionality.
 *
 * @group text
 */
class ValidateDateTest extends UnitTestCase {

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
  }

  /**
   * @expectedException \Drupal\entity_reference_timer\Plugin\Field\Exception\NoStartButEndException
   */
  public function testNoStartButEndException(): void {
    $element['publish_timer']['#value'] = 1;
    $element['start_date']['#value']['date'] = NULL;
    $element['start_date']['#value']['time'] = '090909AM';
    $element['end_date']['#value']['date'] = '2018-11-12';
    $element['end_date']['#value']['time'] = '090909AM';

    InputValidator::handleDateInputException($element);
  }

  /**
   * @expectedException \Drupal\entity_reference_timer\Plugin\Field\Exception\StartAfterEndException
   */
  public function testStartAfterEndException(): void {
    $element['publish_timer']['#value'] = 1;
    $element['start_date']['#value']['date'] = '2018-11-12';
    $element['start_date']['#value']['time'] = '090909AM';
    $element['end_date']['#value']['date'] = '2016-11-12';
    $element['end_date']['#value']['time'] = '090909AM';

    InputValidator::handleDateInputException($element);
  }

  public function testNoInput(): void {
    $element['publish_timer']['#value'] = 1;
    $element['start_date']['#value']['date'] = NULL;
    $element['start_date']['#value']['time'] = NULL;
    $element['end_date']['#value']['date'] = NULL;
    $element['end_date']['#value']['time'] = NULL;

    self::assertNull(InputValidator::handleDateInputException($element));
  }

  public function testPublishTimerDisabled(): void {
    $element['publish_timer']['#value'] = 0;
    $element['start_date']['#value']['date'] = '2018-11-12';
    $element['start_date']['#value']['time'] = '090909AM';
    $element['end_date']['#value']['date'] = '2016-11-12';
    $element['end_date']['#value']['time'] = '090909AM';

    self::assertNull(InputValidator::handleDateInputException($element));
  }

  public function testStartDateOnly(): void {
    $element['publish_timer']['#value'] = 0;
    $element['start_date']['#value']['date'] = '2018-11-12';
    $element['start_date']['#value']['time'] = '090909AM';
    $element['end_date']['#value']['date'] = NULL;
    $element['end_date']['#value']['time'] = NULL;

    self::assertNull(InputValidator::handleDateInputException($element));
  }

}
