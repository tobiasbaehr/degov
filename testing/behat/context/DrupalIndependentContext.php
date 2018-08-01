<?php

namespace Drupal\degov\Behat\Context;

use Behat\Mink\Exception\ResponseTextException;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Testwork\Hook\HookDispatcher;
use WebDriver\Exception\StaleElementReference;


class DrupalIndependentContext extends RawMinkContext {

	private const MAX_DURATION_SECONDS = 1200;
	private const MAX_SHORT_DURATION_SECONDS = 10;

	/**
	 * {@inheritdoc}
	 */
	public function setDispatcher(HookDispatcher $dispatcher) {
		$this->dispatcher = $dispatcher;
	}

	/**
	 * @param $name
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function getDrupalSelector($name) {
		$text = $this->getDrupalParameter('selectors');
		if (!isset($text[$name])) {
			throw new \Exception(sprintf('No such selector configured: %s', $name));
		}
		return $text[$name];
	}

	/**
	 * Get driver's random generator.
	 */
	public function getRandom() {
		return $this->getDriver()->getRandom();
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
	 * @Then /^I should see text matching "([^"]*)" after a while$/
	 */
	public function iShouldSeeTextAfterAWhile($text)
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
	 * @Then /^I submit the form$/
	 */
	public function iSubmitTheForm()
	{
		$session = $this->getSession(); // get the mink session
		$element = $session->getPage()->find(
			'xpath',
			$session->getSelectorsHandler()->selectorToXpath('xpath', '//*[@type="submit"]')
		); // runs the actual query and returns the element

		// errors must not pass silently
		if (null === $element) {
			throw new \InvalidArgumentException(sprintf('Could not evaluate XPath: "%s"', '//*[@type="submit"]'));
		}

		// ok, let's click on it
		$element->click();
	}

}
