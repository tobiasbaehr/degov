<?php

namespace Drupal\degov\Behat\Context;

use Behat\Gherkin\Node\TableNode;
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
   * @Then /^I proof that Drupal module "([^"]*)" is installed$/
   */
  public function proofDrupalModuleIsInstalled($moduleName) {
    /**
     * @var ModuleHandler $moduleHandler
     */
    $moduleHandler = \Drupal::service('module_handler');
    if (!$moduleHandler->moduleExists($moduleName)){
      throw new ResponseTextException("Drupal module $moduleName is not installed.", $this->getSession());
    }

    return TRUE;
  }

  /**
   * Proofs multiple Drupal modules installation.
   *
   * Provide module data in the following format:
   *
   * | machine_name |
   * | webform      |
   * | devel        |
   *
   * @Given I proof that the following Drupal modules are installed:
   */
  public function proofMultipleDrupalModulesAreInstalled(TableNode $modulesTable) {
    $rowsHash = $modulesTable->getRowsHash();
    $moduleMachineNames = array_keys($rowsHash);
    if ($moduleMachineNames['0'] !== 'machine_name') {
      throw new ResponseTextException("You must specify a 'machine_name' table column identifier.", $this->getSession());
    }
    unset($moduleMachineNames['0']);

    foreach ($moduleMachineNames as $moduleMachineName) {
      /**
       * @var ModuleHandler $moduleHandler
       */
      $moduleHandler = \Drupal::service('module_handler');
      if (!$moduleHandler->moduleExists($moduleMachineName)){
        throw new ResponseTextException("Drupal module '$moduleMachineName'' is not installed.", $this->getSession());
      }

      return TRUE;
    }
  }

}
