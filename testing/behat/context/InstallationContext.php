<?php

namespace Drupal\degov\Behat\Context;

use Drupal\degov\Behat\Context\Exception\TextNotFoundException;
use Behat\MinkExtension\Context\RawMinkContext;
use Drupal\degov\Behat\Context\Traits\DebugOutputTrait;
use Drupal\degov\Behat\Context\Traits\ErrorTrait;
use WebDriver\Exception\StaleElementReference;


class InstallationContext extends RawMinkContext {

  use ErrorTrait;

  use DebugOutputTrait;

  private const MAX_DURATION_SECONDS = 1200;

  /**
   * @Then /^task "([^"]*)" is done$/
   */
  public function taskIsDone($text) {
    try {
      $startTime = \time();
      do {
        $doneTask = [
          'Sprache auswählen'                => 'body > div > div > aside > ol > li:nth-child(1).done',
          'Systemvoraussetzungen überprüfen' => 'body > div > div > aside > ol > li:nth-child(2).done',
          'Datenbank einrichten'             => 'body > div > div > aside > ol > li:nth-child(3).done',
          'Website installieren'             => 'body > div > div > aside > ol > li:nth-child(4).done',
          'Übersetzungen konfigurieren'      => 'body > div > div > aside > ol > li:nth-child(5).done',
          'Website konfigurieren'            => 'body > div > div > aside > ol > li:nth-child(6).done',
          'Install deGov - Base'             => 'body > div > div > aside > ol > li:nth-child(7).done',
          'Install deGov - Media'            => 'body > div > div > aside > ol > li:nth-child(8).done',
          'Install deGov - Theme'            => 'body > div > div > aside > ol > li:nth-child(9).done',
          'Finalize installation'            => 'body > div > div > aside > ol > li:nth-child(13).done',
          'Übersetzungen abschließen'        => 'body > div > div > aside > ol > li:nth-child(14).done',
          'deGov wurde erfolgreich installiert.' => 'body.path-frontpage'
        ];

        $task = $this->getSession()->getPage()->findAll('css', $doneTask[$text]);
        $this->checkErrorsOnCurrentPage();

        if (\count($task) > 0) {
          return true;
        }
      } while (time() - $startTime < self::MAX_DURATION_SECONDS);

      try {
        throw new TextNotFoundException(
          sprintf('Task "%s" could not been finished after %s seconds', $text, self::MAX_DURATION_SECONDS),
          $this->getSession()
        );
      } catch (TextNotFoundException $exception) {
        $this->generateCurrentBrowserViewDebuggingOutput(__METHOD__);
        throw $exception;
      }

    } catch (StaleElementReference $e) {
      return TRUE;
    }

  }

  /**
   * @Given /^i test$/
   */
  public function iTest() {
    print_r($this->getSession()->getPage()->getContent());
  }

}
