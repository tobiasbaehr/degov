<?php

declare(strict_types=1);

namespace Drupal\degov\Behat\Context\Exception;

/**
 * class MaxDurationElapsedException
 */
class MaxDurationElapsedException extends \Exception {

  public const MAX_DURATION_SECONDS = 120;

  public function __construct(int $maxDurationInSeconds = MaxDurationElapsedException::MAX_DURATION_SECONDS) {
    parent::__construct(
      sprintf('The maximum duration of %d seconds has been exceeded.', $maxDurationInSeconds)
    );
  }

}
