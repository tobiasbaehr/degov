<?php

namespace Drupal\degov\Behat\Context;

use Behat\Behat\Tester\Exception\PendingException;
use Drupal\Driver\DrupalDriver;
use Drupal\taxonomy\Entity\Vocabulary;

class FeatureContext extends ExtendedRawDrupalContext {

  public function __construct() {
    $driver = new DrupalDriver(DRUPAL_ROOT, '');
    $driver->setCoreFromVersion();

    // Bootstrap Drupal.
    $driver->bootstrap();
  }

  /**
   * @Given /^I create vocabulary with name "([^"]*)" and vid "([^"]*)"$/
   */
  public function createVocabulary($name, $vid) {
    $vocabulary = \Drupal::entityQuery('taxonomy_vocabulary')
      ->condition('vid', $vid)
      ->execute();

    if (empty($vocabulary)) {
      $vocabulary = Vocabulary::create([
        'name' => $name,
        'vid' => $vid,
      ]);
      $vocabulary->save();
    }
  }

  /**
   * @Given /^I create (\d+) nodes of type "([^"]*)"$/
   */
  public function iCreateNodesOfType($number, $type) {
    for ($i = 0; $i <= $number; $i++) {
      $node = new \stdClass();
      $node->type = $type;
      $node->title = $this->createRandomString();
      $node->body = $this->createRandomString();
      $this->nodeCreate($node);
    }
  }

  private function createRandomString($length = 10) {
    return substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", $length)), 0, $length);
  }

  /**
   * @Given Node access records are rebuild.
   */
  public function nodeAccessRecordsAreRebuild() {
    node_access_rebuild();
  }

  /**
   * @Then /^wait (\d+) seconds$/
   */
  public function waitSeconds($secondsNumber) {
    $this->getSession()->wait($secondsNumber * 1000);
  }

  /**
   * @Then /^I select index (\d+) in dropdown named "([^"]*)"$/
   */
  public function selectIndexInDropdown($index, $name) {
    $this->getSession()
      ->evaluateScript('document.getElementsByName("' . $name . '")[0].selectedIndex = ' . $index . ';');
  }

  /**
   * @Then /^I open node edit form by node title "([^"]*)"$/
   * @param string $title
   */
  public function openNodeEditFormByTitle($title) {
    $query = \Drupal::service('database')->select('node_field_data', 'nfd')
      ->fields('nfd', ['nid'])
      ->condition('nfd.title', $title);

    $this->visitPath('/node/' . $query->execute()->fetchField() . '/edit');
  }

  /**
   * @Then /^I open node view by node title "([^"]*)"$/
   * @param string $title
   */
  public function openNodeViewByTitle($title) {
    $query = \Drupal::service('database')->select('node_field_data', 'nfd')
      ->fields('nfd', ['nid'])
      ->condition('nfd.title', $title);

    $this->visitPath('/node/' . $query->execute()->fetchField());
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
   * @Then /^I select "([^"]*)" in "([^"]*)"$/
   * @param string $label
   * @param string $id
   */
  public function selectOption($label, $id) {
    $page = $this->getSession()->getPage();
    $selectElement = $page->find('xpath', '//select[@id = "' . $id . '"]');
    $selectElement->selectOption($label);
  }

  /**
   * @Then /^I click by CSS class "([^"]*)"$/
   * @param string $class
   */
  public function clickByCSSClass($class) {
    $page = $this->getSession()->getPage();
    $button = $page->find('xpath', '//input[contains(@class, ' . $class . ')]');
    $button->click();
  }

  /**
   * @Then /^I click by XPath "([^"]*)"$/
   * @param string $xpath
   */
  public function iClickByXpath($xpath) {
    $session = $this->getSession(); // get the mink session
    $element = $session->getPage()->find(
      'xpath',
      $session->getSelectorsHandler()->selectorToXpath('xpath', $xpath)
    ); // runs the actual query and returns the element

    // errors must not pass silently
    if (NULL === $element) {
      throw new \InvalidArgumentException(sprintf('Could not evaluate XPath: "%s"', $xpath));
    }

    // ok, let's click on it
    $element->click();
  }

  /**
   * @Then /^I should see "([^"]*)" exactly "([^"]*)" times$/
   */
  public function iShouldSeeTextSoManyTimes($sText, $iExpected) {
    $sContent = $this->getSession()->getPage()->getText();
    $iFound = substr_count($sContent, $sText);
    if ($iExpected != $iFound) {
      throw new \Exception('Found ' . $iFound . ' occurences of "' . $sText . '" when expecting ' . $iExpected);
    }
  }

  /**
   * @Then /^I proof Checkbox with id "([^"]*)" has value"([^"]*)"$/
   */
  public function iProofCheckboxWithIdHasValue($id, $checkfor) {
    $Page = $this->getSession()->getPage();
    $isChecked = $Page->find('css', 'input[type="checkbox"]:checked#' . $id);
    $status = ($isChecked) ? "checked" : "unchecked";
    if (
      ($checkfor == "checked" && $isChecked == TRUE)
      ||
      ($checkfor == "unchecked" && $isChecked == FALSE)
    ) {
      return TRUE;
    }
    else {
      throw new \Exception('Checkbox was ' . $status . ' when expecting ' . $checkfor);
      return FALSE;
    }
  }

  /**
   * @Then /^I am installing the "([^"]*)" module$/
   */
  public function iAmInstallingTheModule($moduleName) {
    \Drupal::service('module_installer')->install([$moduleName]);
  }

  /**
   * @Given /^I should see the option "([^"]*)" in "([^"]*)"$/
   * @param $value
   * @param $id
   */
  public function iShouldSeeTheOptionIn($value, $id) {
    $page = $this->getSession()->getPage();
    /** @var $selectElement \Behat\Mink\Element\NodeElement */
    $selectElement = $page->find('xpath', '//select[@id = "' . $id . '"]');
    $element = $selectElement->find('css', 'option[value=' . $value . ']');
    if (!$element) {
      throw new \Exception("There is no option with the value '$value' in the select '$id'");
    }
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
   * @Given /^I submit a form by id "([^"]*)"$/
   */
  public function iSubmitAFormById($Id) {
    $page = $this->getSession()->getPage();
    $element = $page->find('css',"form#${Id}");
    $element->submit();
  }
}
