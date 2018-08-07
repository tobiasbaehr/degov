<?php

namespace Drupal\degov\Behat\Context;

use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ResponseTextException;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\Core\ProxyClass\Extension\ModuleInstaller;
use Drupal\DrupalExtension\Context\RawDrupalContext;


class ModuleContext extends RawDrupalContext {

  /**
   * @Then /^I proof that Drupal module "([^"]*)" is installed$/
   */
  public function proofDrupalModuleIsInstalled($moduleName): void {
    if (!$this->getModuleHandler()->moduleExists($moduleName)){
      throw new ResponseTextException("Drupal module $moduleName is not installed.", $this->getSession());
    }
  }

  /**
   * Proofs multiple Drupal modules installation.
   *
   * Provide module data in the following format:
   *
   * | webform      |
   * | devel        |
   *
   * @Given I proof that the following Drupal modules are installed:
   */
  public function proofMultipleDrupalModulesAreInstalled(TableNode $modulesTable): void {
    $rowsHash = $modulesTable->getRowsHash();
    $moduleMachineNames = array_keys($rowsHash);

    foreach ($moduleMachineNames as $moduleMachineName) {
      if (!$this->getModuleHandler()->moduleExists($moduleMachineName)){
        throw new ResponseTextException("Drupal module '$moduleMachineName' is not installed.", $this->getSession());
      }
    }
  }

  /**
   * @Then /^I am installing the "([^"]*)" module$/
   */
  public function iAmInstallingTheModule(string $moduleName): void {
    $this->getModuleInstaller()->install([$moduleName]);
  }

  /**
   * Installs multiple Drupal modules
   *
   * Provide module data in the following format:
   *
   * | webform      |
   * | devel        |
   *
   * @Given I am installing the following Drupal modules:
   */
  public function installMultipleDrupalModules(TableNode $modulesTable): void {
    $rowsHash = $modulesTable->getRowsHash();
    $moduleMachineNames = array_keys($rowsHash);

    foreach ($moduleMachineNames as $moduleMachineName) {
      $this->getModuleInstaller()->install([$moduleMachineName]);
    }
  }

  protected function getModuleInstaller(): ModuleInstaller {
    return \Drupal::service('module_installer');
  }

  protected function getModuleHandler(): ModuleHandler {
    return \Drupal::service('module_handler');
  }

}
