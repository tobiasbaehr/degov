<?php

namespace degov\Scripts\Robo\Exception;

use Throwable;

class NoInstallationProfileProvided extends \Exception {

  public function __construct($message = 'You must provide an installation profile object.', $code = 0, Throwable $previous = NULL) {
    parent::__construct($message, $code, $previous);
  }

}