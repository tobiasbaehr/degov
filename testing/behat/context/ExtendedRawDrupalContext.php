<?php

namespace Drupal\degov\Behat\Context;

use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Exception\ResponseTextException;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\DrupalExtension\Context\RawDrupalContext;


class ExtendedRawDrupalContext extends RawDrupalContext {

  /**
   * @param int    $seconds
   * @param string $locator
   * @param string $selector
   *
   * @return bool
   * @throws ResponseTextException
   */
  protected function waitSecondsUntilElementAppears($seconds, $locator, $selector = 'css')
  {
    $startTime = time();
    do {
      try {
        $node = $this->getSession()->getPage()->findAll($selector, $locator);
        if (count($node) > 0) {
          return true;
        }
      } catch (ExpectationException $e) {
        /* Intentionally left blank */
      }
    } while (time() - $startTime < $seconds);
    throw new ResponseTextException(
      sprintf('Cannot find the element %s after %s seconds', $locator, $seconds),
      $this->getSession()
    );
  }

  /**
   * @Then header has CSS class for fluid bootstrap layout
   */
  public function headerHasCssClassForFluidBootstrapLayout() : ?bool
  {
    $header = $this->getSession()->getPage()->findAll('css', 'header.container-fluid');
    if (\count($header) > 0) {
      return true;
    } else {
      throw new ResponseTextException('Header does not have CSS class for fluid bootstrap layout.', $this->getSession());
    }
  }

  /**
   * @Then /^Drupal module "([^"]*)" is installed$/
   */
  public function iAmInstallingTheModule($moduleName) {
    /**
     * @var ModuleHandler $moduleHandler
     */
    $moduleHandler = \Drupal::service('module_handler');
    if (!$moduleHandler->moduleExists($moduleName)){
      throw new ResponseTextException("Drupal module $moduleName is not installed.", $this->getSession());
    }

    return TRUE;
  }

}
