<?php

declare(strict_types=1);

namespace Drupal\degov\Behat\Context\Exception;

/**
 * class SocialMediaFeedsNotEnabledException
 */
class SocialMediaFeedsNotEnabledException extends \Exception {

  public function __construct(array $feedNames) {
    $feedNamesString = implode(' ', $feedNames);

    parent::__construct(
      "The following social media could not be determined as enabled via the cookie value: $feedNamesString"
    );
  }

}
