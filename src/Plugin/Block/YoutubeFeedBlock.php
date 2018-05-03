<?php

namespace Drupal\youtube_feed_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\ContextAwarePluginInterface;
use Drupal\degov_social_media_settings\Service\CookieCheck;
use Drupal\degov_theming\Service\Template;
use Drupal\facets\Exception\Exception;
use Drupal\youtube_feed_block\Factory\YouTubeFactory;
use Madcoda\Youtube\Youtube;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Template\TwigEnvironment;

/**
 * Provides a 'YouTubeFeedBlock' block.
 *
 * @Block(
 *  id = "youtube_feed_block",
 *  admin_label = @Translation("Youtube Feed Block"),
 * )
 */
class YoutubeFeedBlock extends BlockBase implements ContextAwarePluginInterface, ContainerFactoryPluginInterface {

  /**
   * @var ConfigFactory
   */
  private $configFactory;

  /**
   * @var TwigEnvironment
   */
  private $twig;

  /**
   * @var CookieCheck
   */
  private $cookieCheck;

  /**
   * @var YouTubeFactory
   */
  private $youtubeFactory;

  public function __construct(
    array $configuration, $plugin_id, $plugin_definition, ConfigFactory $configFactory, YouTubeFactory $youTubeFactory, CookieCheck $cookieCheck, TwigEnvironment $twig
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->configFactory = $configFactory;
    $this->youtubeFactory = $youTubeFactory;
    $this->cookieCheck = $cookieCheck;
    $this->twig = $twig;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('youtube_feed_block.youtube_factory'),
      $container->get('degov_social_media_settings.cookie_check'),
      $container->get('twig')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build['#markup'] = '<div class="youtube-feed-block"></div>';

    return $build;
  }

}

