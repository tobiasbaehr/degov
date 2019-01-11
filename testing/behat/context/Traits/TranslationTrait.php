<?php

namespace Drupal\degov\Behat\Context\Traits;

use Drupal\Core\StringTranslation\TranslatableMarkup;

trait TranslationTrait {

  private $langcode = 'de';

  public function translateString(string $text): string {
    $translateableMarkup = new TranslatableMarkup($text, [], []);
    $translatedString = \Drupal::translation()
      ->translateString($translateableMarkup);
    return $translatedString;
  }

}
