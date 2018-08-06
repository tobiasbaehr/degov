<?php

namespace Drupal\degov\Behat\Context;

use Behat\Mink\Exception\ResponseTextException;
use Drupal\DrupalExtension\Context\RawDrupalContext;


class ExtendedRawDrupalContext extends RawDrupalContext {

  /**
   * @Then header has CSS class for fluid bootstrap layout
   */
  public function headerHasCssClassForFluidBootstrapLayout() : ?bool {
    $header = $this->getSession()->getPage()->findAll('css', 'header.container-fluid');
    if (\count($header) > 0) {
      return true;
    } else {
      throw new ResponseTextException('Header does not have CSS class for fluid bootstrap layout.', $this->getSession());
    }
  }

}
