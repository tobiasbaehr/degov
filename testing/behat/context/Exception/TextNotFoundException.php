<?php

namespace Drupal\degov\Behat\Context\Exception;

use Behat\Mink\Driver\DriverInterface;
use Behat\Mink\Session;


class TextNotFoundException extends \Exception {

  /**
   * @var DriverInterface|Session
   */
  private $session;

  /**
   * @var DriverInterface
   */
  private $driver;

  /**
   * Initializes exception.
   *
   * @param string                  $message   optional message
   * @param DriverInterface|Session $driver    driver instance (or session for BC)
   * @param \Exception|null         $exception expectation exception
   */
  public function __construct($message, $driver, \Exception $exception = null)
  {
    if ($driver instanceof Session) {
      @trigger_error('Passing a Session object to the ExpectationException constructor is deprecated as of Mink 1.7. Pass the driver instead.', E_USER_DEPRECATED);

      $this->session = $driver;
      $this->driver = $driver->getDriver();
    } elseif (!$driver instanceof DriverInterface) {
      // Trigger an exception as we cannot typehint a disjunction
      throw new \InvalidArgumentException('The ExpectationException constructor expects a DriverInterface or a Session.');
    } else {
      $this->driver = $driver;
    }

    if (!$message && null !== $exception) {
      $message = $exception->getMessage();
    }

    parent::__construct($this->getMessageWithAllHTML($message), 0, $exception);
  }

  private function getMessageWithAllHTML(string $message) {
//    return $message . PHP_EOL . PHP_EOL . ' [DEBUG HINT] All HTML markup on the page: ' . PHP_EOL . $this->session->getPage()->getContent();
    return $message;
  }

}
