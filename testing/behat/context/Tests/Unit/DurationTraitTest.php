<?php

declare(strict_types=1);

namespace Drupal\degov\Behat\Context\Tests\Unit;

use Drupal\degov\Behat\Context\Exception\MaxDurationElapsedException;
use Drupal\degov\Behat\Context\Traits\DurationTrait;
use PHPUnit\Framework\TestCase;

/**
 * class DurationTraitTest
 */
class DurationTraitTest extends TestCase {

  use DurationTrait;

  /**
   * @var int
   */
  private $counter;

  public function setUp(): void {
    $this->counter = 0;
    self::resetStartTime();
  }

  public function testMaxDurationNotElapsed(): void {
    while (self::maxDurationNotElapsed() && !$this->countUntilThree()) {
      continue;
    }

    self::assertTrue($this->countUntilThree());
  }

  public function testElapseMaxDuration(): void {
    $this->expectException(MaxDurationElapsedException::class);
    while (self::maxDurationNotElapsed(2) && !$this->countUntilThree(TRUE)) {
      continue;
    }
  }

  private function countUntilThree($sleepThreeSeconds = FALSE) {
    if ($sleepThreeSeconds) {
      sleep(3);
    }

    if ($this->counter === 3) {
      return TRUE;
    }
    $this->counter++;

    return FALSE;
  }

}
