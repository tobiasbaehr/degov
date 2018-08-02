<?php

namespace Drupal\degov\Behat\Context;

use Drupal\block\Entity\Block;
use Drupal\DrupalExtension\Context\RawDrupalContext;


class BlockContext extends RawDrupalContext {

  /**
   * @Then /^I configure and place the deGov social media settings block$/
   */
  public function configureAndPlaceSocialMediaSettings() {
    $block = Block::create([
      'id' => 'social_media_settings_block',
      'theme' => 'degov_base_theme',
      'weight' => 0,
      'status' => TRUE,
      'region' => 'header',
      'plugin' => 'social_media_settings_block',
      'settings' => [
        'id'            => 'social_media_settings_block',
        'label'         => 'Social media settings block',
        'provider'      => 'degov_social_media_settings',
        'label_display' => FALSE,
      ],
      'visibility' => [],
    ]);
    $block->save();
  }

}