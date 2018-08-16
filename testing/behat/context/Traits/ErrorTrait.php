<?php

namespace Drupal\degov\Behat\Context\Traits;

use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Mink\Exception\ResponseTextException;
use Behat\Testwork\Tester\Result\TestResult;

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
   *
   */
  public function takeScreenshotAfterFailedStep(AfterStepScope $scope)
  {
    if (TestResult::FAILED === $scope->getTestResult()) {
      sprintf('Screen shot as base64 "%s"',$this->getSession()->getDriver()->getScreenshot());
    }
  }


}
