<?php

namespace Drupal\degov\Behat\Context;

use Behat\Mink\Exception\ResponseTextException;
use Behat\MinkExtension\Context\RawMinkContext;
use WebDriver\Exception\StaleElementReference;

class InstallationContext extends RawMinkContext {

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
        ];

        $task = $this->getSession()->getPage()->findAll('css', $doneTask[$text]);

        $content = $this->getSession()->getPage()->getText();

        if (substr_count($content, 'Error') > 0) {
          $errorText = 'Error';
        } elseif (substr_count($content, 'Warning') > 0) {
          $errorText = 'Warning';
        } elseif (substr_count($content, 'Notice') > 0) {
          $errorText = 'Notice';
        } elseif (substr_count($content, 'The import failed due for the following reasons:') > 0) {
          $errorText = 'The import failed due for the following reasons:';
        }

        if (!empty($errorText)) {
          throw new ResponseTextException(
            sprintf('Task failed due "%s" text on page', $errorText),
            $this->getSession()
          );
        }

        if (\count($task) > 0) {
          return true;
        }
      } while (time() - $startTime < self::MAX_DURATION_SECONDS);
      throw new ResponseTextException(
        sprintf('Task "%s" could not been finished after %s seconds', $text, self::MAX_DURATION_SECONDS),
        $this->getSession()
      );
    } catch (StaleElementReference $e) {
      return TRUE;
    }

  }

}
