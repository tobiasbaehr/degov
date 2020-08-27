<?php

namespace Drupal\degov_tweets;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Messenger\MessengerInterface;

/**
 * Twitter-API-PHP : Simple PHP wrapper for the v1.1 API.
 *
 * An altered version of the Twitter-API-PHP library.
 */
class TwitterAPIExchange implements TwitterAPIExchangeInterface {

  /**
   * OAuth access token.
   *
   * @var string
   */
  private $oauthAccessToken;

  /**
   * OAuth access token secret.
   *
   * @var string
   */
  private $oauthAccessTokenSecret;

  /**
   * Consumer key.
   *
   * @var string
   */
  private $consumerKey;

  /**
   * Consumer secret.
   *
   * @var string
   */
  private $consumerSecret;

  /**
   * Post fields.
   *
   * @var array
   */
  private $postfields;

  /**
   * Get field.
   *
   * @var string
   */
  private $getfield;

  /**
   * OAuth.
   *
   * @var array
   */
  protected $oauth;

  /**
   * Url.
   *
   * @var string
   */
  public $url;

  /**
   * Request method.
   *
   * @var string
   */
  public $requestMethod;

  /**
   * The messenger.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Definition of LoggerChannel.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  /**
   * TwitterAPIExchange constructor.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger
   *   The logger service.
   */
  public function __construct(MessengerInterface $messenger, LoggerChannelFactoryInterface $logger) {
    $this->logger = $logger->get('degov_tweets');
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public function setSettings(array $settings) {

    if (empty($settings['oauth_access_token'])
      || empty($settings['oauth_access_token_secret'])
      || empty($settings['consumer_key'])
      || empty($settings['consumer_secret'])
    ) {
      $message = 'Make sure you are passing in the correct Twitter parameters.';
      $this->logger->notice($message);
      $this->messenger->addMessage($message, 'warning');
    }

    $this->oauthAccessToken = $settings['oauth_access_token'];
    $this->oauthAccessTokenSecret = $settings['oauth_access_token_secret'];
    $this->consumerKey = $settings['consumer_key'];
    $this->consumerSecret = $settings['consumer_secret'];
  }

  /**
   * Set postfields array, example: array('screen_name' => 'J7mbo')
   *
   * @param array $array
   *   Array of parameters to send to API.
   *
   * @throws \Exception
   *   When you are trying to set both get and post fields.
   *
   * @return TwitterAPIExchange
   *   Instance of self for method chaining
   */
  public function setPostfields(array $array) {
    if (!is_null($this->getGetfield())) {
      $message = 'You can only choose get OR post fields.';
      $this->logger->notice($message);
      $this->messenger->addMessage($message, 'warning');
    }

    if (isset($array['status']) && substr($array['status'], 0, 1) === '@') {
      $array['status'] = sprintf("\0%s", $array['status']);
    }
    foreach ($array as $key => &$value) {
      if (is_bool($value)) {
        $value = ($value === TRUE) ? 'true' : 'false';
      }
    }

    $this->postfields = $array;

    // Rebuild oAuth.
    if (isset($this->oauth['oauth_signature'])) {
      $this->buildOauth($this->url, $this->requestMethod);
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setGetfield(array $getfieldParams) {
    if (!is_null($this->getPostfields())) {
      $message = 'You can only choose get OR post fields.';
      $this->logger->notice($message);
      $this->messenger->addMessage($message, 'warning');
    }

    $this->getfield = '?' . UrlHelper::buildQuery($getfieldParams);

    return $this;
  }

  /**
   * Get getfield string (simple getter)
   *
   * @return string
   *   $this->getfields
   */
  public function getGetfield() {
    return $this->getfield;
  }

  /**
   * Get postfields array (simple getter)
   *
   * @return array
   *   $this->postfields
   */
  public function getPostfields() {
    return $this->postfields;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOauth($url, $requestMethod = 'GET') {
    if (!in_array(strtolower($requestMethod), ['post', 'get'])) {
      $message = 'Request method must be either POST or GET.';
      $this->logger->notice($message);
      $this->messenger->addMessage($message, 'warning');
    }

    $consumer_key = $this->consumerKey;
    $consumer_secret = $this->consumerSecret;
    $oauth_access_token = $this->oauthAccessToken;
    $oauth_access_token_secret = $this->oauthAccessTokenSecret;

    $oauth = [
      'oauth_consumer_key' => $consumer_key,
      'oauth_nonce' => time(),
      'oauth_signature_method' => 'HMAC-SHA1',
      'oauth_token' => $oauth_access_token,
      'oauth_timestamp' => time(),
      'oauth_version' => '1.0',
    ];

    $getfield = $this->getGetfield();

    if (!is_null($getfield)) {
      $getfields = str_replace('?', '', explode('&', $getfield));
      foreach ($getfields as $g) {
        $split = explode('=', $g);
        // In case a null is passed through.
        if (isset($split[1])) {
          $oauth[$split[0]] = urldecode($split[1]);
        }
      }
    }

    $postfields = $this->getPostfields();
    if (!is_null($postfields)) {
      foreach ($postfields as $key => $value) {
        $oauth[$key] = $value;
      }
    }
    $base_info = $this->buildBaseString($url, $requestMethod, $oauth);
    $composite_key = rawurlencode($consumer_secret) . '&' . rawurlencode($oauth_access_token_secret);
    $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, TRUE));
    $oauth['oauth_signature'] = $oauth_signature;

    $this->url = $url;
    $this->requestMethod = $requestMethod;
    $this->oauth = $oauth;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function performRequest(bool $return = TRUE, array $curlOptions = []) {

    $header = [$this->buildAuthorizationHeader($this->oauth), 'Expect:'];
    $getfield = $this->getGetfield();
    $postfields = $this->getPostfields();
    $options = [
      CURLOPT_HTTPHEADER => $header,
      CURLOPT_HEADER => FALSE,
      CURLOPT_URL => $this->url,
      CURLOPT_RETURNTRANSFER => TRUE,
      CURLOPT_TIMEOUT => 10,
    ] + $curlOptions;
    if (!is_null($postfields)) {
      $options[CURLOPT_POSTFIELDS] = http_build_query($postfields);
    }
    else {
      if ($getfield !== '') {
        $options[CURLOPT_URL] .= $getfield;
      }
    }

    $feed = curl_init();
    curl_setopt_array($feed, $options);
    $json = curl_exec($feed);

    if (($error = curl_error($feed)) !== '') {
      $this->logger->notice($error);
      $this->messenger->addMessage($error, 'warning');
      if ($feed === 'ressource') {
        curl_close($feed);
      }
    }

    if ($feed === 'ressource') {
      curl_close($feed);
    }

    return substr_count($json, 'errors') > 0 ? NULL : $json;
  }

  /**
   * Private method to generate the base string used by cURL.
   *
   * @param string $baseURI
   *   Base URI.
   * @param string $method
   *   Method.
   * @param array $params
   *   Params.
   *
   * @return string
   *   Built base string
   */
  private function buildBaseString($baseURI, $method, array $params) {
    $return = [];
    ksort($params);
    foreach ($params as $key => $value) {
      $return[] = rawurlencode($key) . '=' . rawurlencode($value);
    }

    return $method . "&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $return));
  }

  /**
   * Private method to generate authorization header used by cURL.
   *
   * @param array $oauth
   *   Array of oauth data generated by buildOauth()
   *
   * @return string
   *   $return Header used by cURL for request
   */
  private function buildAuthorizationHeader(array $oauth) {
    $return = 'Authorization: OAuth ';
    $values = [];

    foreach ($oauth as $key => $value) {
      if (in_array($key, [
        'oauth_consumer_key',
        'oauth_nonce',
        'oauth_signature',
        'oauth_signature_method',
        'oauth_timestamp',
        'oauth_token',
        'oauth_version',
      ])) {
        $values[] = "$key=\"" . rawurlencode($value) . "\"";
      }
    }

    $return .= implode(', ', $values);
    return $return;
  }

  /**
   * Helper method to perform our request.
   *
   * @param string $url
   *   Url.
   * @param string $method
   *   Method.
   * @param array|null $data
   *   Data.
   * @param array $curlOptions
   *   Curl options.
   *
   * @throws \Exception
   *
   * @return string
   *   The json response from the server
   */
  public function request($url, $method = 'get', $data = NULL, array $curlOptions = []) {
    if (strtolower($method) === 'get') {
      $this->setGetfield($data);
    }
    else {
      $this->setPostfields($data);
    }
    return $this->buildOauth($url, $method)->performRequest(TRUE, $curlOptions);
  }

}
