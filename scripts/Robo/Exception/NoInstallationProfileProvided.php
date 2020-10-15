<?php

declare(strict_types=1);

namespace degov\Scripts\Robo\Exception;

/**
 * Class NoInstallationProfileProvided.
 */
class NoInstallationProfileProvided extends \Exception {

  /**
   * NoInstallationProfileProvided constructor.
   */
  public function __construct(string $message = '', int $code = 0, \Throwable $previous = NULL) {
    if (!$message) {
      $message = 'You must provide an installation profile object.';
    }

    parent::__construct($message, $code, $previous);
  }

}
