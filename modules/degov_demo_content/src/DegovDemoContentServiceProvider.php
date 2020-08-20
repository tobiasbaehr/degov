<?php

declare(strict_types=1);

namespace Drupal\degov_demo_content;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Drupal\degov_demo_content\SocialMedia\Instagram;
use Drupal\degov_demo_content\SocialMedia\Twitter;
use Drupal\degov_demo_content\SocialMedia\Youtube;

/**
 * Class DegovDemoContentServiceProvider
 *
 * @package Drupal\degov_demo_content
 */
class DegovDemoContentServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    if ($container->hasDefinition('degov_social_media_youtube.youtube')) {
      $definition = $container->getDefinition('degov_social_media_youtube.youtube');
      $definition->setClass(Youtube::class);
    }
    if ($container->hasDefinition('degov_social_media_instagram.instagram')) {
      $definition = $container->getDefinition('degov_social_media_instagram.instagram');
      $definition->setClass(Instagram::class);
    }
    if ($container->hasDefinition('degov_tweets.twitter')) {
      $definition = $container->getDefinition('degov_tweets.twitter');
      $definition->setClass(Twitter::class);
    }

  }

}
