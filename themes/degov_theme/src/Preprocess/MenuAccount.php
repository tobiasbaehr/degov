<?php
/**
 * @file MenuAccount.php.
 */

namespace Drupal\degov_theme\Preprocess;

/**
 * Class MenuAccount
 *
 * @package Drupal\degov_theme\Preprocess
 */
class MenuAccount {

  /**
   * Preprocess the menu theme.
   *
   * @param array $vars
   */
  static public function preprocess(array &$vars) {
    if ($vars['menu_name'] === 'account') {
      array_walk($vars['items'], function (&$item) {
        if (!empty($item['title']['icon'])) {
          $item['title']['icon']['#attributes']['class'][] = 'ml-2';
        }

        /** @var \Drupal\Core\Url $url */
        $url = $item['url'];
        if (($url->isRouted())
          && $url->getRouteName() === 'user.login'
          && !\Drupal::currentUser()->isAnonymous()) {
          if (isset($item['title']['#markup'])) {
            $item['title']['#markup'] = t('My Account');
          }
          else {
            $item['title'] = t('My Account');
          }
        }
      });
    }
  }
}
