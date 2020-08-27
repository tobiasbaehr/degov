<?php

namespace Drupal\degov_social_media_instagram;

/**
 * Class Instagram.
 *
 * @package Drupal\degov_social_media_instagram
 */
interface InstagramInterface {

  public function getMedias($username, $count = 20, $maxId = '');

  public function getAccount($username);

}
