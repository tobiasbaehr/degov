<?php

namespace Drupal\degov\Behat\Context\Traits;

use Behat\Mink\Exception\ResponseTextException;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\degov\Behat\Context\Exception\TextNotFoundException;

trait TranslationTrait {

	private $langcode = 'de';

	public function translateString(string $text): string {
		$translateableMarkup = new TranslatableMarkup($text, [], []);
		$translatedString = \Drupal::translation()->translateString($translateableMarkup);
		return $translatedString;
	}

}
