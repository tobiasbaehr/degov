<?php

declare(strict_types=1);

namespace Drupal\degov\Behat\Context\Exception;

use Behat\Mink\Driver\DriverInterface;
use Behat\Mink\Session;

/**
 * Class TextNotFoundException.
 */
class PageCouldNotBeFullyLoadedException extends \Exception {

  /**
   * @var string
   *   The error message
   */
  protected $message = 'The page could not be fully loaded.';

  /**
   * Session.
   *
   * @var \Behat\Mink\Driver\DriverInterface|Session
   */
  private $session;

  /**
   * Driver.
   *
   * @var \Behat\Mink\Driver\DriverInterface
   */
  private $driver;

  /**
   * Initializes exception.
   *
   * @param int $durationInSeconds
   *   Duration in seconds.
   * @param \Behat\Mink\Driver\DriverInterface|Session $driver
   *   Driver instance (or session for BC)
   * @param \Exception|null $previous
   *   Expectation exception.
   */
  public function __construct($driver, \Exception $previous = NULL) {

    if ($driver instanceof Session) {
      @trigger_error('Passing a Session object to the ExpectationException constructor is deprecated as of Mink 1.7. Pass the driver instead.', E_USER_DEPRECATED);

      $this->session = $driver;
      $this->driver = $driver->getDriver();
    }
    elseif (!$driver instanceof DriverInterface) {
      // Trigger an exception as we cannot typehint a disjunction.
      throw new \InvalidArgumentException('The ExpectationException constructor expects a DriverInterface or a Session.');
    }
    else {
      $this->driver = $driver;
    }

    parent::__construct($this->message, 0, $previous);
    $this->message = parent::getMessage() . PHP_EOL;
    $this->message .= ($previous !== NULL) ? $previous->getMessage() : '';
  }

}
