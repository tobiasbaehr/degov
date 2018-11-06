<?php

namespace Drupal\degov_theming\TwigExtension;

/**
 * Class LinkExtension.
 */
class LinkExtension extends \Twig_Extension {

  /**
   * Returns a list of the filters provided by this class.
   *
   * @return array|\Twig_SimpleFilter[]
   *   The filters.
   */
  public function getFilters(): array {
    return [
      'get_menu_item_target' => new \Twig_Filter_Function([
        $this,
        'getMenuItemTarget',
      ]),
      'get_menu_item_rel'    => new \Twig_Filter_Function([
        $this,
        'getMenuItemRel',
      ]),
    ];
  }

  /**
   * Returns the target attribute for a given menu item.
   *
   * @param array $menu_item
   *   The menu item.
   *
   * @return \Twig_Markup
   *   The target attribute, or an empty string if nothing set.
   */
  public function getMenuItemTarget(array $menu_item): \Twig_Markup {
    $target = FALSE;
    if (!empty($menu_item['external']) && (bool) $menu_item['external'] === TRUE) {
      $target = '_blank';
    }
    if (!empty($menu_item['class']['options']['attributes']['target'])) {
      $target = $menu_item['class']['options']['attributes']['target'];
    }
    if ($target) {
      return new \Twig_Markup(sprintf(' target="%s"', $target), 'UTF-8');
    }
    return new \Twig_Markup('', 'UTF-8');
  }

  /**
   * Returns the rel attribute for a given menu item.
   *
   * @param array $menu_item
   *   The menu item.
   *
   * @return \Twig_Markup
   *   The rel attribute, or an empty string if nothing set.
   */
  public function getMenuItemRel(array $menu_item): \Twig_Markup {
    if (!empty($menu_item['class']['options']['attributes']['rel'])) {
      return new \Twig_Markup(sprintf(' rel="%s"', $menu_item['class']['options']['attributes']['rel']), 'UTF-8');
    }
    return new \Twig_Markup('', 'UTF-8');
  }

}
