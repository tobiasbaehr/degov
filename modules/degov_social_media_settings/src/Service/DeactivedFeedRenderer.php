<?php

namespace Drupal\degov_social_media_settings\Service;

use Drupal\Core\Logger\LoggerChannelFactory;
use Drupal\Core\Template\TwigEnvironment;
use Twig_Error_Loader;

class DeactivedFeedRenderer {

  /**
   * @var TwigEnvironment
   */
  private $twig;

  /**
   * @var LoggerChannelFactory
   */
  private $logger;

  public function __construct(TwigEnvironment $twig, LoggerChannelFactory $logger) {
    $this->twig = $twig;
  }

  public function render() {
    try {
      $template = $this->twig->load(drupal_get_path('module', 'degov_social_media_settings') . '/templates/deactivated-feed.html.twig');
    } catch(Twig_Error_Loader $e) {
      $this->logger->get('degov_social_media_settings')->error('Template ');
    }

    return $template->render(['roles' => $roles, 'users' => $users]);
  }

}