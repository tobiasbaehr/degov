<?php

namespace Drupal\degov_social_media_settings\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'SocialMediaSettingsBlock' block.
 *
 * @Block(
 *  id = "social_media_settings_block",
 *  admin_label = @Translation("Social media settings block"),
 *  category = @Translation("Social media")
 * )
 */
final class SocialMediaSettingsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /** @var \Drupal\Core\Config\ConfigFactoryInterface*/
  private $configFactory;

  /**
   * SocialMediaSettingsBlock constructor.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $configFactory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $configFactory;
  }

  /**
   * @inheritDoc
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->configFactory->get('degov_common.default_settings');
    $build = [];
    $build['#theme'] = 'degov_social_media_settings_block';
    $build['#social_media_netiquette_url'] = $config->get('netiquette_url');
    $build['#social_media_privacy_url'] = $config->get('privacy_url');
    $build['#attached']['library'] = ['degov_social_media_settings/process'];

    $sourceNames = $this->configFactory->get('degov_social_media_settings.default')->get('sources');
    $build['#social_media_sources'] = $sourceNames;
    foreach ($sourceNames as $id => $name) {
      $sourceNames[$id] = $this->t('Social media source @name is disabled.', ['@name' => $name]);
    }

    $degov_social_media_settings = [
      'link' => $this->t('You can enable it in the <a role="button" href="#" data-toggle="modal" data-target="#social-media-settings" class="js-social-media-settings-open social-media-settings--menu-item">social media settings</a>.'),
      'cookie' => $this->t('After accepting our cookie policy, you can enable it.'),
      'mediaMessages' => $sourceNames,
    ];
    $sources = [];
    foreach ($build['#social_media_sources'] as $key => $value) {
      $sources[$key] = FALSE;
    }
    $degov_social_media_settings += ['sources' => $sources];
    $build['#attached']['drupalSettings'] = ['degov_social_media_settings' => $degov_social_media_settings];
    return $build;
  }

}
