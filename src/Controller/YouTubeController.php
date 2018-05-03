<?php

namespace Drupal\youtube_feed_block\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\youtube_feed_block\Service\YouTubeFeed;
use Symfony\Component\DependencyInjection\ContainerInterface;

class YouTubeController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * @var YouTubeFeed
   */
  private $youTubeFeed;

  public function __construct(YouTubeFeed $youTubeFeed) {
    $this->youTubeFeed = $youTubeFeed;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('youtube_feed_block.youtube_feed')
    );
  }

  public function renderFeed() {
    return $this->youTubeFeed->computeFeed();
  }

}