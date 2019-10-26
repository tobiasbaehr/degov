<?php

namespace Drupal\degov\Behat\Context\Traits;

use Behat\Behat\Hook\Scope\AfterStepScope;
use Drupal\degov\Behat\Context\Exception\TextNotFoundException;

trait DebugOutputTrait {

  /**
   * @var int
   */
  private $screenshotCount = 0;

  /**
   * @var int
   */
  private $htmlDumpCount = 0;

  /**
   * @AfterStep
   */
  public function generateStepDebuggingOutputWithScope(AfterStepScope $scope): void {
    if (!$scope->getTestResult()->isPassed()) {
      $this->saveHtmlOfPage($scope->getFeature()->getTitle());
      $this->saveScreenshotAsFile($scope->getFeature()->getTitle());
    }
  }

  /**
   * @AfterStep
   */
  public function closeSymfonyToolbar(AfterStepScope $scope): void  {
    $this->getSession()->executeScript('if (document.querySelector(".sf-toolbar a.hide-button") !== null) { document.querySelector(".sf-toolbar a.hide-button").click(); }');
  }

  public function generateCurrentBrowserViewDebuggingOutput(string $name): void {
    $this->saveHtmlOfPage($name);
    $this->saveScreenshotAsFile($name);
  }

  public function saveHtmlOfPage(string $name): void {
    $dateTime = new \DateTime();
    $filename = $name . '-' . date_format($dateTime, 'H:i:s') . '.html';

    file_put_contents($this->computePath() . '/' . $filename, $this->getSession()->getPage()->getContent());
  }

  public function saveScreenshotAsFile(string $name): void {
    $dateTime = new \DateTime();
    $filename = sprintf('%s-%s.png', $name, date_format($dateTime, 'H:i:s'));

    file_put_contents($this->computePath() . '/' . $filename, $this->getSession()->getScreenshot());
  }

  /**
   * @AfterStep
   */
  public function checkErrorsWithScope(AfterStepScope $scope): void {
    if (empty(self::$errorTexts)) {
      return;
    }

    foreach (self::$errorTexts as $errorText) {
      $pageText = $this->getSession()->getPage()->getText();
      if (substr_count(strtolower($pageText), strtolower($errorText)) > 0) {

        try {
          throw new TextNotFoundException(
            sprintf('Task failed due "%s" text on page \'', $pageText.'\''),
            $this->getSession()
          );
        } catch(TextNotFoundException $exception) {
          $this->generateCurrentBrowserViewDebuggingOutput($scope->getFeature()->getTitle());

          throw $exception;
        }
      }
    }
  }

  public function checkErrorsOnCurrentPage(): void {
    if (empty(self::$errorTexts)) {
      return;
    }

    foreach (self::$errorTexts as $errorText) {
      $pageText = $this->getSession()->getPage()->getText();
      if (substr_count(strtolower($pageText), strtolower($errorText)) > 0) {

        try {
          throw new TextNotFoundException(
            sprintf('Task failed due "%s" text on page \'', $pageText.'\''),
            $this->getSession()
          );
        } catch(TextNotFoundException $exception) {
          $this->generateCurrentBrowserViewDebuggingOutput(__METHOD__);
          throw $exception;
        }
      }
    }
  }

  private function computePath(): string {
    if (!empty($path = getenv('CI_ROOT_DIR'))) {
      return $path;
    }

    if (\Drupal::hasContainer()) {
      return \Drupal::root() . '/..';
    }

    return __DIR__ . '/../../../../../../../../';
  }

}