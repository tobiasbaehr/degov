<?php

namespace Drupal\degov\Behat\Context;

use Drupal\degov\Behat\Context\Traits\TranslationTrait;
use Drupal\DrupalExtension\Context\RawDrupalContext;
use Drupal\node\Entity\Node;

class NodeContentTypeFormContext extends RawDrupalContext {

	use TranslationTrait;

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
  public function iChooseFromTabMenu(string $text): void {
    $page = $this->getSession()->getPage(); // get the mink session
    $found = FALSE;
    $cssClass = "div.vertical-tabs.clearfix ul.vertical-tabs__menu li a";
    $elements = $page->findAll('css', $cssClass);

    $counter = 0;
    foreach ($elements as $element) {
      $tmp = $element->find('css', "strong");
      $selectedText = $tmp->getText();

      if ($selectedText === $text) {
        $found = TRUE;
        $this->getSession()
          ->executeScript('jQuery("' . $cssClass . '")[' . $counter . '].click()');
      }
      $counter++;
    }
  }

	/**
	 * @Given /^I choose "([^"]*)" via translation from tab menu$/
	 */
	public function iChooseTranslatedFromTabMenu(string $text): void {

		$text = $this->translateString($text);

		$page = $this->getSession()->getPage(); // get the mink session
		$found = FALSE;
		$cssClass = "div.vertical-tabs.clearfix ul.vertical-tabs__menu li a";
		$elements = $page->findAll('css', $cssClass);

		$counter = 0;
		foreach ($elements as $element) {
			$tmp = $element->find('css', "strong");
			$selectedText = $tmp->getText();

			if ($selectedText === $text) {
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
   * @Given /^I select "([^"]*)" via translation in uppercase from rightpane$/
   */
  public function iSelectFromRightpane(string $text): void {
    $divLayout = "div.layout-region.layout-region-node-secondary div.entity-meta.js-form-wrapper.form-wrapper details";
    $page = $this->getSession()->getPage(); // get the mink session
    $elements = $page->findAll("css", $divLayout);

    foreach ($elements as $element) {
      if ($element->getText() === trim(mb_strtoupper($this->translateString($text)))) {
        $pane = $element->find("css", "summary");
        $pane->click();
        $checkbox = $element->find('css', '.details-wrapper label.option');
        $checkbox->click();
      }
    }
  }

  /**
   * @Given /^I choose "([^"]*)" via translation in selectModerationBox$/
   */
  public function iChooseInSelectModerationBox(string $text): void {
    $page = $this->getSession()->getPage();
    $optionElements = $page->findAll('css', 'div.container-inline select option');

    foreach ($optionElements as $optionElement) {
      if ($optionElement->getText() === trim($this->translateString($text))) {
        $optionElement->click();
      }
    }
  }

  /**
   * @Given /^I proof content with title "([^"]*)" has moderation state "([^"]*)"$/
   *   "([^"]*)"$/
   */
  public function iProofContentWithTitleHasModerationState($title, $state) {
    $Ids = \Drupal::entityQuery('node')
      ->condition('title', $title)->execute();

    foreach($Ids as $Id) {
      $NodeState = Node::load($Id)->moderation_state->value;
      if($state === $NodeState) {
        return;
      }
    }
    throw new \Exception("No content with title '$title' and moderation state '$state'");

  }

}
