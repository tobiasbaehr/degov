<?php

namespace Drupal\degov\Behat\Context\Traits;

use Behat\Mink\Exception\ResponseTextException;

trait ErrorTrait {

  private static $errorTexts = [
    'Error',
    'Warning',
    'Notice',
    'The import failed due for the following reasons:',
    'Es wurde eine nicht erlaubte Auswahl entdeckt.',
    'An AJAX HTTP error occurred.',
  ];

  /**
   * @afterStep
   */
  public function checkErrors(): void {
    foreach (self::$errorTexts as $errorText) {
      $pageText = $this->getSession()->getPage()->getText();
      if (substr_count($pageText, $errorText) > 0) {
        $matches = [];
        preg_match($errorText . '[a-zA-Z 0-9\r\n\"\\\'\:\{\}]*/$mi', 'foobarbaz', $matches, PREG_OFFSET_CAPTURE);
        if ($matches && is_array($matches)) {
          throw new ResponseTextException(
            sprintf('Task failed due "%s" text on page', $matches[0]),
            $this->getSession()
          );
        }
      }
    }
  }

}
