<?php

namespace Drupal\degov_demo_content\Command;

use Drupal\Console\Annotations\DrupalCommand;
use Drupal\Console\Core\Command\ContainerAwareCommand;
use Drupal\degov_demo_content\Generator\MediaGenerator;
use Drupal\degov_demo_content\Generator\MenuItemGenerator;
use Drupal\degov_demo_content\Generator\NodeGenerator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ResetCommand.
 *
 * @DrupalCommand (
 *     extension="degov_demo_content",
 *     extensionType="module"
 * )
 */
class ResetConsoleCommand extends ContainerAwareCommand {

  /**
   * The deGov Demo Content MediaGenerator.
   *
   * @var \Drupal\degov_demo_content\Generator\MediaGenerator
   */
  private $mediaGenerator;

  /**
   * The deGov Demo Content NodeGenerator.
   *
   * @var \Drupal\degov_demo_content\Generator\NodeGenerator
   */
  private $nodeGenerator;

  /**
   * The deGov Demo Content MenuItemGenerator.
   *
   * @var \Drupal\degov_demo_content\Generator\MenuItemGenerator
   */
  private $menuItemGenerator;

  /**
   * ResetConsoleCommand constructor.
   *
   * @param \Drupal\degov_demo_content\Command\MediaGenerator $mediaGenerator
   *   The deGov Demo Content MediaGenerator.
   * @param \Drupal\degov_demo_content\Command\NodeGenerator $nodeGenerator
   *   The deGov Demo Content NodeGenerator.
   * @param \Drupal\degov_demo_content\Command\MenuItemGenerator $menuItemGenerator
   *   The deGov Demo Content MenuItemGenerator.
   */
  public function __construct(MediaGenerator $mediaGenerator, NodeGenerator $nodeGenerator, MenuItemGenerator $menuItemGenerator) {
    parent::__construct();
    $this->mediaGenerator = $mediaGenerator;
    $this->nodeGenerator = $nodeGenerator;
    $this->menuItemGenerator = $menuItemGenerator;
  }

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('degov_demo_content:reset')
      ->setDescription($this->trans('commands.degov_demo_content.reset.description'));
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->mediaGenerator->resetContent();
    $this->nodeGenerator->resetContent();
    $this->menuItemGenerator->resetContent();
    $this->getIo()
      ->info($this->trans('commands.degov_demo_content.reset.messages.success'));
  }

}
