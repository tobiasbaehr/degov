<?php

namespace Drupal\degov_theme\Preprocess;


use Drupal\Core\StringTranslation\StringTranslationTrait;

class ViewsExposedFormSearchPageFulltext {

  use StringTranslationTrait;

  public function preprocess(array &$vars, $hook): void {
    if (!in_array($hook, ['views_exposed_form__search_content'])) {
      return;
    }
    $vars['form']['volltext']['#title_display'] = 'invisible';
    $vars['form']['volltext']['#attributes']['placeholder'] = $this->t('Search term');
    $vars['form']['volltext']['#field_suffix'] = '<i class="fas fa-search"></i>';
    $vars['form']['volltext']['#wrapper_attributes']['class'][] = 'd-inline-block';
    $vars["form"]["actions"]['#attributes']['class'][] = 'd-inline-block';
  }
}
