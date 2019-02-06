<?php

namespace Drupal\degov_config_integrity\Command;

use Drupal\Console\Core\Command\ContainerAwareCommand;
use Drupal\degov_config_integrity\DegovModuleIntegrityChecker;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Drupal\Console\Annotations\DrupalCommand;

/**
 * Class ResetCommand.
 *
 * @DrupalCommand (
 *     extension="degov_config_integrity",
 *     extensionType="module"
 * )
 */
class DegovConfigIntegrityCheckConsoleCommand extends ContainerAwareCommand {

  /**
   * The module integrity checker.
   *
   * @var \Drupal\degov_config_integrity\DegovModuleIntegrityChecker
   */
  private $moduleIntegrityChecker;

  /**
   * ResetConsoleCommand constructor.
   */
  public function __construct(DegovModuleIntegrityChecker $moduleIntegrityChecker) {
    parent::__construct();
    $this->moduleIntegrityChecker = $moduleIntegrityChecker;
  }

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('config:diff:installed-modules')
      ->setDescription(t('Check for missing configuration.'));
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->getIo()->info(t('deGov Configuration Integrity Check running…'));
    $configurationIntegrityIntact = TRUE;
    foreach ($this->moduleIntegrityChecker->checkIntegrity() as $index => $module) {
      foreach ($module as $key => $messages) {
        $this->getIo()->newLine();
        $this->getIo()->warningLite(t('Module @module: Configuration is missing', ['@module' => $key]));
        foreach($messages as $message) {
          $this->getIo()->info($message);
        }
        $configurationIntegrityIntact = FALSE;
      }
    }
    if($configurationIntegrityIntact) {
      $this->getIo()->successLite('All expected configuration seems to be in place.');
    }
  }

}
