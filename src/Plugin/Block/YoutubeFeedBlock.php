<?php

namespace Drupal\youtube_feed_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\ContextAwarePluginInterface;
use Drupal\degov_social_media_settings\Service\CookieCheck;
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
    $build = [];

    $config = \Drupal::service('config.factory')
      ->get('youtube_feed_block.settings');

    $apiKey = $config->get('api_key');
    $channelName = $config->get('channel');
    $numberOfVideos = $config->get('number_of_videos');

    try {
      $youtube = $this->youtubeFactory->create($apiKey);;

      $channelID = $youtube->getChannelByName($channelName)->id;
      if(!$channelID) $channelID = $channelName;
      $videos = $youtube->searchChannelVideos('', $channelID, $numberOfVideos, 'date');


      foreach ($videos as $video) {
        $info = $youtube->getVideoInfo($video->id->videoId);

        $build['youtube_feed_block'][] = [
          '#theme' => 'youtube_feed_block',
          '#title' => $video->snippet->title,
          '#likes' => $info->statistics->likeCount,
          '#views' => $info->statistics->viewCount,
          '#comments' => $info->statistics->commentCount,
          '#videoID' => $video->id->videoId,
          '#thumbnail' => $video->snippet->thumbnails->default->url,
          '#cache' => ['max-age' => (60 * 60)],
          '#link_display' => $this->_shortDescription("https://youtube.com/watch?v=" . $video->id->videoId, 32, '...'),
        ];
      }
    }
    catch(Exception $e) {
      $build['#markup'] = '<span class="error">'.$e."</span>";

    }

    return $build;
  }

  function _shortDescription($string, $maxLenth, $replacement) {
    if (strlen($string) > $maxLenth) {
      return substr($string, 0, $maxLenth) . $replacement;
    }
    else {
      return $string;
    }
  }
}

