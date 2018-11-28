<?php

namespace Drupal\degov\Behat\Context\Traits;

use Behat\Mink\Exception\ResponseTextException;

/**
 * Trait ErrorTrait
 *
 * @package Drupal\degov\Behat\Context\Traits
 */
trait ErrorTrait {

  private static $errorTexts = [
    'Error',
    'Warning',
    'Notice',
    'The import failed due for the following reasons:',
    'Es wurde eine nicht erlaubte Auswahl entdeckt.',
    'An AJAX HTTP error occurred.',
    'Nicht erfüllte Systemvoraussetzungen'
  ];

  /**
   * @afterStep
   */
  public function checkErrors(): void {
    foreach (self::$errorTexts as $errorText) {
      $pageText = $this->getSession()->getPage()->getText();
      if (substr_count(strtolower($pageText), strtolower($errorText)) > 0) {
        throw new ResponseTextException(
          sprintf('Task failed due "%s" text on page \'', $pageText.'\''),
          $this->getSession()
        );
      }
    }
  }

}
