<?php

namespace Drupal\degov\Behat\Context;

use Behat\Mink\Exception\ResponseTextException;
use Drupal\block\Entity\Block;
use Drupal\Core\Config\Config;
use Drupal\DrupalExtension\Context\RawDrupalContext;
use Drupal\Core\Config\ConfigFactory;

class BlockContext extends RawDrupalContext {

  /**
   * @Then /^I configure and place the deGov social media settings block$/
   */
  public function configureAndPlaceSocialMediaSettings() {
    $block = Block::create([
      'id' => 'social_media_settings_block',
      'theme' => 'degov_theme',
      'weight' => 0,
      'status' => TRUE,
      'region' => 'header',
      'plugin' => 'social_media_settings_block',
      'settings' => [
        'id'            => 'social_media_settings_block',
        'label'         => 'Social Media Settings',
        'provider'      => 'degov_social_media_settings',
        'label_display' => FALSE,
      ],
      'visibility' => [],
    ]);
    $block->save();
  }

  /**
   * @Then /^I configure and place the Instagram feed block$/
   */
  public function configureAndPlaceInstagram() {
    /** @var ConfigFactory $configFactory $configFactory */
    $configFactory = \Drupal::service('config.factory');
    $config = $configFactory->getEditable('degov_social_media_instagram.settings');
    $config->set('user', 'ig_bundestag')->save();

    $block = Block::create([
      'id' => 'instagramfeedblock',
      'theme' => 'degov_theme',
      'weight' => 0,
      'status' => TRUE,
      'region' => 'content',
      'plugin' => 'degov_social_media_instagram',
      'settings' => [
        'id'            => 'degov_social_media_instagram',
        'label'         => 'Instagram feed block',
        'provider'      => 'degov_social_media_instagram',
        'label_display' => 'visible',
      ],
      'visibility' => [],
    ]);
    $block->save();
  }

  /**
   * @Then /^I delete any existing blocks with comma separated ids "([^"]*)"$/
   */
  public function deleteBlocks(string $blockIds) {
    $blockIds = explode(',', $blockIds);

    if (!is_array($blockIds) || count($blockIds) < 1) {
      throw new ResponseTextException(
        'Could not determine any block ids. You must pass a comma separated list with machine names.',
        $this->getSession()
      );
    }

    foreach ($blockIds as $blockId) {
      $block = Block::load(trim($blockId));
      if ($block instanceof Block) {
        $block->delete();
      }
    }

  }

}
