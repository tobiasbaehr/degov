<?php

namespace Drupal\degov\Behat\Context;

use Behat\MinkExtension\Context\RawMinkContext;

class DebuggingContext extends RawMinkContext {

  /**
   * @Then /^I print current page HTML markup into pipeline$/
   */
  public function printCurrentPageHTMLMarkup() {
    var_dump($this->getSession()->getPage()->getHtml());
  }

}
