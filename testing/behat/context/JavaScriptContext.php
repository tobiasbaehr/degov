<?php

namespace Drupal\degov\Behat\Context;

use Behat\MinkExtension\Context\RawMinkContext;


class JavaScriptContext extends RawMinkContext {

  /**
   * @Then /^I select index (\d+) in dropdown named "([^"]*)"$/
   */
  public function selectIndexInDropdown($index, $name) {
    $this->getSession()
      ->evaluateScript('document.getElementsByName("' . $name . '")[0].selectedIndex = ' . $index . ';');
  }

  /**
   * @Then /^I scroll to element with id "([^"]*)"$/
   * @param string $id
   */
  public function iScrollToElementWithId($id) {
    $this->getSession()->executeScript(
      "
                var element = document.getElementById('" . $id . "');
                element.scrollIntoView( true );
            "
    );
  }

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
   * @Then /^I click by selector "([^"]*)" via JavaScript$/
   * @param string $selector
   */
  public function clickBySelector(string $selector)
  {
    $this->getSession()->executeScript("document.querySelector('" . $selector . "').click()");
  }

  /**
   * @Then /^I proof css selector "([^"]*)" has attribute "([^"]*)" with value "([^"]*)"$/
   */
  public function cssSelectorAttributeMatchesValue($selector, $attribute, $value) {
    if ($this->getSession()
        ->evaluateScript("jQuery('$selector').css('$attribute')") === $value) {
      return true;
    }
    else {
      throw new \Exception("CSS selector $selector does not match attribute '$attribute' with value '$value'");
    }
  }

  /**
   * @Given /^I fill in Textarea with "([^"]*)"$/
   */
  public function iFillInTextareaWith($arg1)
  {

    $this->getSession()->executeScript('jQuery("div.form-textarea-wrapper iframe").contents().find("p").text("' . $arg1 . '")');

  }

  /**
   * @Then /^I scroll to bottom$/
   */
  public function iScrollToBottom(): void {
    $this->getSession()
      ->executeScript('window.scrollTo(0,document.body.scrollHeight);');
  }

  /**
   * @Then /^I scroll to top$/
   */
  public function iScrollToTop(): void {
    $this->getSession()
      ->executeScript('window.scrollTo(0,0);');
  }

}
