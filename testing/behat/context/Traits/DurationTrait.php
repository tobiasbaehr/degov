<?php

namespace Drupal\degov\Behat\Context\Traits;

use Drupal\degov\Behat\Context\Exception\MaxDurationElapsedException;

/**
 * trait DurationTrait
 */
trait DurationTrait {

  /**
   * @var int
   */
  public static $startTime = 0;

  /**
   * @beforeStep
   */
  public static function resetStartTime() {
    self::$startTime = time();
  }

  public static function maxDurationNotElapsed(int $maxDurationSeconds = MaxDurationElapsedException::MAX_DURATION_SECONDS): bool {
    if ((time() - self::$startTime) < $maxDurationSeconds) {
      return TRUE;
    }
    throw new MaxDurationElapsedException($maxDurationSeconds);
  }

}
