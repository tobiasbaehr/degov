<?php

namespace Drupal\youtube_feed_block\Service;

use Drupal\Core\Config\ConfigFactory;
use Drupal\degov_social_media_settings\Service\CookieCheck;
use Drupal\degov_theming\Service\Template;
use Drupal\youtube_feed_block\Factory\YouTubeFactory;
use Symfony\Component\HttpFoundation\Response;

class YouTubeFeed {

  /**
   * @var ConfigFactory
   */
  private $configFactory;

  /**
   * @var YouTubeFactory
   */
  private $youTubeFactory;

  /**
   * @var CookieCheck
   */
  private $cookieCheck;

  /**
   * @var Template
   */
  private $template;

  public function __construct(ConfigFactory $configFactory, YouTubeFactory $youTubeFactory, CookieCheck $cookieCheck, Template $template) {
    $this->configFactory = $configFactory;
    $this->youTubeFactory = $youTubeFactory;
    $this->cookieCheck = $cookieCheck;
    $this->template = $template;
  }

  public function computeFeed() {
    $build = [];

    $config = \Drupal::service('config.factory')
      ->get('youtube_feed_block.settings');

    /**
     * @var CookieCheck $cookieCheck
     */
    $cookieCheck = \Drupal::service('degov_social_media_settings.cookie_check');
    if ($cookieCheck->isYouTubeEnabled()) {
      $apiKey = $config->get('api_key');
      $channelName = $config->get('channel');
      $numberOfVideos = $config->get('number_of_videos');

      try {
        $youtube = $this->youTubeFactory->create($apiKey);

        $channelID = $youtube->getChannelByName($channelName)->id;
        if(!$channelID) $channelID = $channelName;
        $videos = $youtube->searchChannelVideos('', $channelID, $numberOfVideos, 'date');

        $html = '';

        foreach ($videos as $video) {
          $info = $youtube->getVideoInfo($video->id->videoId);

          $html .= $this->template->render('youtube_feed_block', 'templates/youtube-feed-block.html.twig', [
            'title' => $video->snippet->title,
            'likes' => $info->statistics->likeCount,
            'views' => $info->statistics->viewCount,
            'comments' => $info->statistics->commentCount,
            'videoID' => $video->id->videoId,
            'thumbnail' => $video->snippet->thumbnails->default->url,
            'cache' => ['max-age' => (60 * 60)],
            'link_display' => $this->shortDescription("https://youtube.com/watch?v=" . $video->id->videoId, 32, '...'),
          ]);
        }
      }
      catch(\Exception $e) {
        $html = '<span class="error">'.$e."</span>";
      }
    } else {
      $html = $this->template->render('degov_social_media_settings', 'templates/deactived-feed.html.twig');
    }

    $response = new Response();

    return $response->setContent($html)->send();
  }

  private function shortDescription($string, $maxLenth, $replacement) {
    if (strlen($string) > $maxLenth) {
      return substr($string, 0, $maxLenth) . $replacement;
    }
    else {
      return $string;
    }
  }

}