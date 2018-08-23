<?php

namespace Drupal\degov\Behat\Context\Traits;

use Behat\Mink\Exception\ResponseTextException;
use Drupal\Core\StringTranslation\TranslatableMarkup;

trait TranslationTrait {

	private $langcode = 'de';

	public function translateString(string $text): string {
		$translateableMarkup = new TranslatableMarkup($text, [], []);
		$translatedString = \Drupal::translation()->translateString($translateableMarkup);

		if ($text === $translatedString) {
			throw new ResponseTextException(
				sprintf('Task failed, because text "%s" could not be translated.', $text),
				$this->getSession()
			);
		}

		return $translatedString;
	}

}