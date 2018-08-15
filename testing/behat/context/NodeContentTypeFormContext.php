<?php

namespace Drupal\degov\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Exception\ResponseTextException;
use Drupal\DrupalExtension\Context\RawDrupalContext;
use Drupal\Core\Entity\Query\QueryInterface;

class NodeContentTypeFormContext extends RawDrupalContext {

  /**
   * @Given /^I see element "([^"]*)" with divclass "([^"]*)"$/
   */
  public function iSeeElementWithDivclass(string $elmnt, $className) {
    $page = $this->getSession()->getPage(); // get the mink session
    $element = $page->find('css', $elmnt . '.' . $className);

    if (!$element) {
      throw new \Exception("Element " . $elmnt . "with classname " . $className . " not found");
    }
  }

  /**
   * @Given /^I choose "([^"]*)" from tab menu$/
   */
  public function iChooseFromTabMenu($arg1) {
    $page = $this->getSession()->getPage(); // get the mink session
    $found = FALSE;
    $cssClass = "div.vertical-tabs.clearfix ul.vertical-tabs__menu li a";
    $elements = $page->findAll('css', $cssClass);

    $counter = 0;
    foreach ($elements as $element) {
      $tmp = $element->find('css', "strong");
      $selectedText = $tmp->getText();

      if ($selectedText === $arg1) {
        $found = TRUE;
        $this->getSession()
          ->executeScript('jQuery("' . $cssClass . '")[' . $counter . '].click()');
      }
      $counter++;
    }
  }

  /**
   * @Given /^I click on togglebutton$/
   */
  public function iClickOnTogglebutton() {
    $this->getSession()
      ->executeScript('jQuery(".dropbutton-widget ul.dropbutton li.dropbutton-toggle button").click()');
  }

  /**
   * @Given /^I select "([^"]*)" from rightpane$/
   */
  public function iSelectFromRightpane($arg1) {
    $divLayout = "div.layout-region.layout-region-node-secondary div.entity-meta.js-form-wrapper.form-wrapper details";
    $page = $this->getSession()->getPage(); // get the mink session
    $elements = $page->findAll("css", $divLayout);
    $counter = 0;
    foreach ($elements as $element) {
      if ($element->getText() === trim($arg1)) {
        $pane = $element->find("css", "summary");
        $pane->click();
        $checkbox = $element->find('css', '.details-wrapper label.option');
        $checkbox->click();
      }
    }
  }

  /**
   * @Given /^I choose "([^"]*)" in selectModerationBox$/
   */
  public function iChooseInSelectModerationBox($arg1) {
    $page = $this->getSession()->getPage(); // get the mink session
    $optionElements = $page->findAll("css", "div.container-inline select option");

    foreach ($optionElements as $optionElement) {
      if ($optionElement->getText() === trim($arg1)) {
        $optionElement->click();
      }
    }
  }

}