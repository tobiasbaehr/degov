<?php

declare(strict_types=1);

namespace Drupal\degov_demo_content\SocialMedia;

use Drupal\degov_tweets\TwitterAPIExchangeInterface;

/**
 * Class Twitter
 *
 * @package Drupal\degov_demo_content\SocialMedia
 */
class Twitter implements TwitterAPIExchangeInterface {

  use SocialMediaAssetsTrait;

  /**
   * {@inheritDoc}
   */
  public function setSettings(array $settings) {
    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function setGetfield(array $params) {
    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function performRequest(bool $return = TRUE, array $curlOptions = []) {
    return self::getDataFromFile('twitter', 'perform_request.txt');
  }

  /**
   * {@inheritDoc}
   */
  public function buildOauth(string $url, string $requestMethod = 'GET') {
    return $this;
  }

}
