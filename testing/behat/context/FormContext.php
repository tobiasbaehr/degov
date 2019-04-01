<?php

namespace Drupal\degov\Behat\Context;

use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\MinkExtension\ServiceContainer\MinkExtension;
use Drupal\degov\Behat\Context\Traits\TranslationTrait;


class FormContext extends RawMinkContext {

	use TranslationTrait;

  /**
   * @Then /^I check checkbox with id "([^"]*)" by JavaScript$/
   * @param string $id
   */
  public function checkCheckboxWithJS($id) {
    $this->getSession()->executeScript(
      "
                document.getElementById('" . $id . "').checked = true;
            "
    );
  }

  /**
   * @Then /^I check checkbox by selector "([^"]*)" via JavaScript$/
   * @param string $selector
   */
  public function checkCheckboxBySelector(string $selector)
  {
    $this->getSession()->executeScript(
      "
                document.querySelector('" . $selector . "').checked = true;
            "
    );
  }

  /**
   * @Then /^I check checkbox by value "([^"]*)" via JavaScript$/
   * @param string $value
   */
  public function checkCheckboxByValue(string $value)
  {
    $this->getSession()->executeScript(
      "
                document.querySelector('input[value=" . $value . "]').checked = true;
            "
    );
  }

  /**
   * @Given /^I fill in Textarea with "([^"]*)"$/
   */
  public function iFillInTextareaWith($arg1)
  {

    $this->getSession()->executeScript('jQuery("div.form-textarea-wrapper iframe").contents().find("p").text("' . $arg1 . '")');

  }

  /**
   * @Given /^I submit a form by id "([^"]*)"$/
   */
  public function iSubmitAFormById($Id) {
    $page = $this->getSession()->getPage();
    $element = $page->find('css', "form#${Id}");
    $element->submit();
  }

  /**
   * @Given /^I should not see the option "([^"]*)" in "([^"]*)"$/
   * @param $value
   * @param $id
   *
   * @throws \Exception
   */
  public function iShouldNotSeeTheOptionIn($value, $id) {
    $page = $this->getSession()->getPage();
    /** @var $selectElement \Behat\Mink\Element\NodeElement */
    $selectElement = $page->find('xpath', '//select[@id = "' . $id . '"]');
    $element = $selectElement->find('css', 'option[value=' . $value . ']');
    if ($element) {
      throw new \Exception("There is an option with the value '$value' in the select '$id'");
    }
  }

  /**
   * @Then /^I check checkbox with id "([^"]*)"$/
   * @param string $id
   */
  public function checkCheckbox($id) {
    $page = $this->getSession()->getPage();
    $selectElement = $page->find('xpath', '//input[@id = "' . $id . '"]');

    $selectElement->check();
  }

  /**
   * @Then /^I uncheck checkbox with id "([^"]*)"$/
   * @param string $id
   */
  public function uncheckCheckbox($id) {
    $page = $this->getSession()->getPage();
    $selectElement = $page->find('xpath', '//input[@id = "' . $id . '"]');

    $selectElement->uncheck();
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

  /**
   * @Then /^I select "([^"]*)" in "([^"]*)"$/
   */
  public function selectOption($label, $id) {
    $page = $this->getSession()->getPage();
    $selectElement = $page->find('xpath', '//select[@id = "' . $id . '"]');
    $selectElement->selectOption($label);
  }

  /**
   * @Then /^I assert dropdown named "([^"]*)" contains the following text-value pairs:$/
   *
   * Provide data in the following format:
   *
   * | text                | value       |
   * | Teaser kleines Bild | small_image |
   * | Teaser langer Text  | long_text   |
   * | Teaser schmal       | slim        |
   * | Vorschau            | preview     |
   */
  public function assertDropdown(string $nameAttributeValue , TableNode $table): void {
    $rowsHash = $table->getRowsHash();
    unset($rowsHash['text']);

    $selector = "select[name='$nameAttributeValue']";
    $node = $this->getSession()->getPage()->find('css', $selector);

    if (null === $node) {
      if (is_array($selector)) {
        $selector = implode(' ', $selector);
      }

      throw new ElementNotFoundException($this->getSession()->getDriver(), 'element', 'css', $selector);
    }

    $html = $node->getHtml();

    $htmlParts = explode('</option>', $html);
    if(strpos($html, 'value="_none"')) {
      array_shift($htmlParts);
    }
    array_pop($htmlParts);

    if (count($htmlParts) !== count($rowsHash)) {
      print_r($rowsHash);
      throw new \Exception(sprintf('Table items number does not match found option values number. Expected %s, found %s', count($rowsHash), count($htmlParts)));
    }

    foreach ($rowsHash as $text => $value) {
      $found = FALSE;
      $htmlPartItems = count($htmlParts) - 1;
      for ($i = 0; $i <= $htmlPartItems; ++$i) {
        if (strpos($htmlParts[$i], $text) && (empty($value) || strpos($htmlParts[$i], $value))) {
          $found = TRUE;
        }
      }
      if ($found === FALSE) {
        throw new \Exception("Text '$text' and value '$value' not found in given options.");
      }
    }
  }

	/**
	 * @Then /^I assert dropbutton actions with css selector "([^"]*)" contains the following name-value pairs:$/
	 *
	 * Provide data in the following format:
	 *
	 * | value                            | name                                              |
	 * | FAQ hinzufügen                   | field_content_paragraphs_faq_add_more             |
	 * | FAQ / Akkordion Liste hinzufügen | field_content_paragraphs_faq_list_add_more        |
	 * | Medienreferenz hinzufügen        | field_content_paragraphs_media_reference_add_more |
	 */
	public function assertDropbutton(string $cssSelector , TableNode $table): void {
		$rowsHash = $table->getRowsHash();
		unset($rowsHash['text']);

		$node = $this->getSession()->getPage()->find('css', $cssSelector);

		if (null === $node) {
			throw new ElementNotFoundException($this->getSession()->getDriver(), 'element', 'css', $cssSelector);
		}

		$html = $node->getHtml();

		$htmlParts = explode('</li>', $html);

		// Remove last element which is empty
		array_pop($htmlParts);

		if (count($htmlParts) !== count($rowsHash) - 1) {
      print_r($htmlParts);
      throw new \Exception(sprintf('Table items number does not match found option values number. (expected: %s, found: %s)', (count($rowsHash) - 1), count($htmlParts)));
		}

		foreach ($rowsHash as $text => $value) {
			$found = FALSE;
			foreach($htmlParts as $htmlPart) {
        if (strpos($htmlPart, $text) && strpos($htmlPart, $value)) {
          $found = TRUE;
        }
      }

			if ($found === FALSE) {
				throw new \Exception("Text '$text' and value '$value' not found in given options.");
			}
		}
	}

	/**
	 * @When /^I press button with label "([^"]*)" via translated text$/
	 */
	public function pressButtonTranslate(string $button) {
		$this->getSession()->getPage()->pressButton($this->translateString($button));
	}

	/**
   * @Then I should see the input with the name :input_name and the value :input_value checked
   */
	public function iShouldSeeTheInputWithTheNameAndTheValueChecked(string $input_name, string $input_value) {
	  $radio_button = $this
      ->getSession()
      ->getPage()
      ->findAll('xpath', '//input[@name and contains(@name, "' . $input_name . '") and @value and @value="' . $input_value . '" and @checked and @checked="checked"]');

	  if(count($radio_button) > 0) {
	    return true;
    }

    throw new \Exception(sprintf('Element "%s" with value "%s" not found!', $input_name, $input_value));
  }

  /**
   * Fills in form field with specified id|name|label|value
   * Example: When I fill in "username" with: "bwayne"
   * Example: And I fill in "bwayne" for "username"
   *
   * @When /^(?:|I )fill in "(?P<field>(?:[^"]|\\")*)" via translated text with "(?P<value>(?:[^"]|\\")*)"$/
   * @When /^(?:|I )fill in "(?P<field>(?:[^"]|\\")*)" via translated text with:$/
   * @When /^(?:|I )fill in "(?P<value>(?:[^"]|\\")*)" via translated text for "(?P<field>(?:[^"]|\\")*)"$/
   */
  public function fillField($field, $value)
  {
    $field = $this->fixStepArgument($this->translateString($field));
    $value = $this->fixStepArgument($value);
    $this->getSession()->getPage()->fillField($field, $value);
  }

  /**
   * Returns fixed step argument (with \\" replaced back to ")
   *
   * @param string $argument
   *
   * @return string
   */
  protected function fixStepArgument($argument)
  {
    return str_replace('\\"', '"', $argument);
  }
}
