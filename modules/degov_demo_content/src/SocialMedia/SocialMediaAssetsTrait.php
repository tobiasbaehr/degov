<?php

namespace Drupal\degov_demo_content\SocialMedia;

/**
 * Trait SocialMediaAssetsTrait.
 *
 * @package Drupal\degov_demo_content
 */
trait SocialMediaAssetsTrait {

  /**
   * Returns the content from storage file.
   *
   * @param string $provider
   *   The name of the social media provider.
   * @param string $fileName
   *   The name of file.
   *
   * @return mixed
   *   Unserialized data.
   */
  private static function getDataFromFile(string $provider, string $fileName) {
    $path = [
      drupal_get_path('module', 'degov_demo_content'),
      'fixtures',
      'social_media',
      $provider,
      $fileName,
    ];
    // TODO refactor this.
    $filePath = implode('/', $path);
    $content = file_get_contents($filePath);
    $options = ['allowed_classes' => TRUE];

    // $data = \unserialize($content, $options);
    //
    //    if ($fileName === 'medias.txt') {
    //    $instagram = new \InstagramScraper\Instagram();
    //    /** @var \InstagramScraper\Model\Media $media */
    //    foreach ($data as $key => $media) {
    //    $data[$key] = $instagram->getMediaById($media->getId());
    //    }
    //    $new_data = serialize($data);
    //    file_put_contents($filePath, $new_data);
    //    #return $data;
    //    }
    return \unserialize($content, $options);
  }

}
