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

  /**
   * Switches out of an frame, into the main window.
   * @When I go back to the main window
   */
  public function exitFrame() {
    $this->getSession()->switchToWindow();
  }

}
