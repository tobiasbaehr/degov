<?php

namespace Drupal\degov\Behat\Context;

use Behat\Mink\Exception\ResponseTextException;
use Drupal\block\Entity\Block;
use Drupal\DrupalExtension\Context\RawDrupalContext;
use Drupal\Core\Config\ConfigFactory;

class BlockContext extends RawDrupalContext {

  /**
   * @Then /^I configure and place the Simplenews signup block$/
   */
  public function configureAndPlaceSimplenewsSignup() {
    /** @var ConfigFactory $configFactory $configFactory */
    $configFactory = \Drupal::service('config.factory');
    $config = $configFactory->getEditable('degov_social_media_instagram.settings');
    $config->set('user', 'ig_bundestag')->save();

    $block = Block::create([
      'id' => 'simplenewsabonnement',
      'uniqueId' => '1234',
      'theme' => 'degov_theme',
      'weight' => 0,
      'status' => TRUE,
      'region' => 'content',
      'plugin' => 'simplenews_subscription_block',
      'settings' => [
        'id'            => 'simplenews_subscription_block',
        'label'         => 'Simplenews Abonnement',
        'provider'      => 'simplenews',
        'label_display' => 'visible',
        'newsletters'   => ['default' => 'default'],
        'unique_id' => 'test1234',
      ],
      'visibility' => [],
    ]);
    $block->save();
  }

  /**
   * @Then /^I configure and place the main menu block$/
   */
  public function configureAndPlaceMainMenu() {
   /** @var ConfigFactory $configFactory $configFactory */
    $block = Block::load('main_menu');

    if(!empty($block) && $block instanceof Block) {
      $block->delete();
    }

    $block = Block::create([
      'id' => 'main_menu',
      'uniqueId' => '2345',
      'theme' => 'degov_theme',
      'weight' => 0,
      'status' => TRUE,
      'region' => 'navigation',
      'plugin' => 'system_menu_block:main',
      'settings' => [
        'id'            => 'system_menu_block:main',
        'label'         => 'Main menu',
        'provider'      => 'system',
        'label_display' => FALSE,
        'level'         => 1,
        'depth'         => 0,
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
