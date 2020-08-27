<?php

declare(strict_types=1);

namespace Drupal\degov_tweets;

/**
 * Interface TwitterAPIExchangeInterface
 *
 * @package Drupal\degov_tweets
 */
interface TwitterAPIExchangeInterface {

  /**
   * Set settings for the API access object.
   *
   * Requires an array of settings::oauth access token,
   * oauth access token secret, consumer key, consumer secret.
   * These are all available by creating your own application on
   * dev.twitter.com. Requires the cURL library.
   *
   * @param array $settings
   *   Settings for TwitterAPIExchange.
   */
  public function setSettings(array $settings);

  /**
   * Set getfield string, example: '?screen_name=J7mbo'.
   *
   * @param array $getfieldParams
   *   Key and value pairs.
   *
   * @throws \Exception
   *
   * @return \Drupal\degov_tweets\TwitterAPIExchange
   *   Instance of self for method chaining
   */
  public function setGetfield(array $params);

  /**
   * Perform the actual data retrieval from the API.
   *
   * @param bool $return
   *   If true, returns data. This is left in for backward
   *   compatibility reasons.
   * @param array $curlOptions
   *   Additional Curl options for this request.
   *
   * @throws \Exception
   *
   * @return string
   *   json If $return param is true, returns json data.
   */
  public function performRequest(bool $return = TRUE, array $curlOptions = []);

  /**
   * Build OAuth.
   *
   * Build the Oauth object using params set in construct and additionals
   * passed to this method. For v1.1, see: https://dev.twitter.com/docs/api/1.1.
   *
   * @param string $url
   *   The API url to use.
   *   Example: https://api.twitter.com/1.1/search/tweets.json.
   * @param string $requestMethod
   *   Either POST or GET.
   *
   * @throws \Exception
   *
   * @return \Drupal\degov_tweets\TwitterAPIExchange
   *   Instance of self for method chaining
   */
  public function buildOauth(string $url, string $requestMethod = 'GET');

}
