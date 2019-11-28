<?php

namespace Drupal\entity_reference_timer\Plugin\Field\Exception;

use Throwable;

class StartAfterEndException extends \Exception {

  public function __construct($message = '', $code = 0, Throwable $previous = null) {
    parent::__construct($message, $code, $previous);
  }

}