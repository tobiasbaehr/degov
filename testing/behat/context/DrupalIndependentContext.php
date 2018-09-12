<?php

namespace Drupal\degov\Behat\Context;

use Behat\Mink\Exception\ElementTextException;
use Behat\Mink\Exception\ResponseTextException;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Testwork\Hook\HookDispatcher;
use Drupal\degov\Behat\Context\Traits\TranslationTrait;
use WebDriver\Exception\StaleElementReference;


class DrupalIndependentContext extends RawMinkContext {

	use TranslationTrait;

	private const MAX_DURATION_SECONDS = 1200;
	private const MAX_SHORT_DURATION_SECONDS = 10;

  private $dispatcher;

  /**
	 * {@inheritdoc}
	 */
	public function setDispatcher(HookDispatcher $dispatcher) {
		$this->dispatcher = $dispatcher;
	}

	/**
	 * @Then /^I should see "([^"]*)" exactly "([^"]*)" times$/
	 */
	public function iShouldSeeTextSoManyTimes($sText, $iExpected)
	{
		$sContent = $this->getSession()->getPage()->getText();
		$iFound = substr_count($sContent, $sText);
		if ($iExpected != $iFound) {
			throw new \Exception('Found '.$iFound.' occurences of "'.$sText.'" when expecting '.$iExpected);
		}
	}

	/**
	 * @Then /^I should see text matching "([^"]*)" via translated text in uppercase after a while$/
	 */
	public function iShouldSeeTranslatedUppercaseTextAfterAWhile(string $text): bool
	{
		$translatedText = mb_strtoupper($this->translateString($text));

		try {
			$startTime = time();
			do {
				$content = $this->getSession()->getPage()->getText();
				if (substr_count($content, $translatedText) > 0) {
					return true;
				}
			} while (time() - $startTime < self::MAX_DURATION_SECONDS);
			throw new ResponseTextException(
				sprintf('Could not find text %s after %s seconds', $translatedText, self::MAX_DURATION_SECONDS),
				$this->getSession()
			);
		} catch (StaleElementReference $e) {
			return true;
		}
	}

	/**
	 * @Then /^I should see text matching "([^"]*)" via translated text after a while$/
	 */
	public function iShouldSeeTranslatedTextAfterAWhile(string $text): bool
	{
		$translatedText = $this->translateString($text);

		try {
			$startTime = time();
			do {
				$content = $this->getSession()->getPage()->getText();
				if (substr_count($content, $translatedText) > 0) {
					return true;
				}
			} while (time() - $startTime < self::MAX_DURATION_SECONDS);
			throw new ResponseTextException(
				sprintf('Could not find text %s after %s seconds', $translatedText, self::MAX_DURATION_SECONDS),
				$this->getSession()
			);
		} catch (StaleElementReference $e) {
			return true;
		}
	}

	/**
	 * @Then /^I should see text matching "([^"]*)" after a while$/
	 */
	public function iShouldSeeTextAfterAWhile(string $text): bool
	{
		try {
			$startTime = time();
			do {
				$content = $this->getSession()->getPage()->getText();
				if (substr_count($content, $text) > 0) {
					return true;
				}
			} while (time() - $startTime < self::MAX_DURATION_SECONDS);
			throw new ResponseTextException(
				sprintf('Could not find text %s after %s seconds', $text, self::MAX_DURATION_SECONDS),
				$this->getSession()
			);
		} catch (StaleElementReference $e) {
			return true;
		}
	}

  /**
   * @Then /^I should see HTML content matching "([^"]*)"$/
   */
  public function iShouldSeeHTMLContentMatching(string $content)
  {
    $html = $this->getSession()->getPage()->getHtml();
    if (substr_count($html, $content) > 0) {
      return true;
    }

    throw new ResponseTextException(
      sprintf('HTML does not contain content "%s"', $content),
      $this->getSession());
  }

  /**
   * @Then /^I should not see HTML content matching "([^"]*)"$/
   */
  public function iShouldNotSeeHTMLContent($html)
  {
    $content = $this->getSession()->getPage()->getText();
    if (substr_count($content, $html) === 0) {
      return true;
    }
  }

  /**
   * @Then /^I should see HTML content matching "([^"]*)" after a while$/
   */
  public function iShouldSeeHTMLContentMatchingAfterWhile($text)
  {
    try {
      $startTime = time();
      do {
        $content = $this->getSession()->getPage()->getHtml();
        if (substr_count($content, $text) > 0) {
          return true;
        }
      } while (time() - $startTime < self::MAX_DURATION_SECONDS);
      throw new ResponseTextException(
        sprintf('Could not find text %s after %s seconds', $text, self::MAX_DURATION_SECONDS),
        $this->getSession()
      );
    } catch (StaleElementReference $e) {
      return true;
    }
  }

	/**
	 * @Then /^I should not see text matching "([^"]*)" after a while$/
	 */
	public function iShouldNotSeeTextAfterAWhile($text)
	{
		$startTime = time();
		do {
			$content = $this->getSession()->getPage()->getText();
			if (substr_count($content, $text) === 0) {
				return true;
			}
		} while (time() - $startTime < self::MAX_SHORT_DURATION_SECONDS);
		throw new ResponseTextException(
			sprintf('Could find text %s after %s seconds', $text, self::MAX_SHORT_DURATION_SECONDS),
			$this->getSession()
		);
	}

  /**
   * @Then /^wait (\d+) seconds$/
   */
  public function waitSeconds($secondsNumber) {
    $this->getSession()->wait($secondsNumber * 1000);
  }

  /**
   * @Then the HTML title should show the page title and the distribution title
   */
  public function theHtmlTitleShouldShowThePageTitleAndTheDistributionTitle() {
    return $this->elementWithSelectorShouldMatchPattern('css', 'html>head>title', "/^[^|]+ | [^|]+$/");
  }

  private function elementWithSelectorShouldMatchPattern($selector_type, $locator, $pattern) {
    $element = $this->getSession()->getPage()->find($selector_type, $locator);
    if(preg_match($pattern, $element->getHtml())) {
      return true;
    }
    throw new ResponseTextException(sprintf('The text of the element "%s" ("%s") did not match the pattern "%s"', $locator, $element->getHtml(), $pattern), $this->getSession());
  }
}
