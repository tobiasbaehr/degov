<?php

namespace Drupal\degov\Behat\Context\Traits;

use Behat\Mink\Exception\ResponseTextException;
use Drupal\degov\Behat\Context\DrupalContext;

trait ErrorTrait  {

  private static $errorTexts = [
    'Error',
    'Warning',
    'Notice',
    'The import failed due for the following reasons:',
    'Es wurde eine nicht erlaubte Auswahl entdeckt.',
  ];

  /**
   * @afterStep
   */
  public function checkErrors(): void {
    foreach (self::$errorTexts as $errorText) {
      if (substr_count($this->getSession()->getPage()->getText(), $errorText) > 0) {
        throw new ResponseTextException(
          sprintf('Task failed due "%s" text on page', $errorText),
          $this->getSession()
        );
      }
    }
  }

  /**
   * @AfterStep
   */
  public function takeScreenshotAfterFailedStep(StepEvent $event)
  {
    if (4 === $this->getResult()) {
      echo $this->getSession()->getDriver()->getScreenshot();
    }
  }


}
