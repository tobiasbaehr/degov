<?php

declare(strict_types=1);

namespace Drupal\degov_demo_content\SocialMedia;

use Drupal\degov_social_media_instagram\InstagramInterface;
use InstagramScraper\Instagram as InstagramScraper;

/**
 * Class Instagram
 *
 * @package Drupal\degov_demo_content\SocialMedia
 */
class Instagram extends InstagramScraper implements InstagramInterface {

  use SocialMediaAssetsTrait;

  /**
   * {@inheritDoc}
   */
  public function getMedias($username, $count = 20, $maxId = '') {
    return self::getDataFromFile('instagram', 'medias.txt');
  }

  /**
   * {@inheritDoc}
   */
  public function getAccount($username) {
    return self::getDataFromFile('instagram', 'account.txt');
  }

}
