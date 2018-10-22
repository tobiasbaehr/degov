<?php

namespace Drupal\degov_demo_content\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Drupal\Console\Core\Command\ContainerAwareCommand;
use Drupal\Console\Annotations\DrupalCommand;

/**
 * Class ResetCommand.
 *
 * @DrupalCommand (
 *     extension="degov_demo_content",
 *     extensionType="module"
 * )
 */
class ResetCommand extends ContainerAwareCommand {

  /**
   * {@inheritdoc}
   */
  protected function configure(
  ) {
    $this
      ->setName('degov_demo_content:reset')
      ->setDescription($this->trans('commands.degov_demo_content.reset.description'));
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->getIo()->info('execute');
    $this->container->get('degov_demo_content.content_generator')->resetMedia();
    $this->getIo()->info($this->trans('commands.degov_demo_content.reset.messages.success'));
  }
}
