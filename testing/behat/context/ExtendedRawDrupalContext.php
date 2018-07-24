<?php

namespace Drupal\Tests\Behat\Context;

use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Exception\ResponseTextException;
use Drupal\DrupalExtension\Context\RawDrupalContext;

/**
 * Class BasicContext
 *
 * @package Drupal\Tests\vsm_testing\Behat\Context
 */
class ExtendedRawDrupalContext extends RawDrupalContext {

  /**
   * @param int    $seconds
   * @param string $locator
   * @param string $selector
   *
   * @return bool
   * @throws ResponseTextException
   */
  protected function waitSecondsUntilElementAppears($seconds, $locator, $selector = 'css')
  {
    $startTime = time();
    do {
      try {
        $node = $this->getSession()->getPage()->findAll($selector, $locator);
        if (count($node) > 0) {
          return true;
        }
      } catch (ExpectationException $e) {
        /* Intentionally left blank */
      }
    } while (time() - $startTime < $seconds);
    throw new ResponseTextException(
      sprintf('Cannot find the element %s after %s seconds', $locator, $seconds),
      $this->getSession()
    );
  }

  /**
   * @Then I scroll to top
   */
  public function iScrollToTop() {
    $this->getSession()->executeScript('window.scrollTo(0,0);');
  }

  /**
   * @Then I scroll to bottom
   */
  public function iScrollToBottom() {
    $this->getSession()
      ->executeScript('window.scrollTo(0,document.body.scrollHeight);');
  }
}
