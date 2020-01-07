<?php

namespace Drupal\degov_search_content\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormState;
use Drupal\Core\Session\AccountInterface;
use Drupal\views\Form\ViewsExposedForm;
use Drupal\views\Views;

/**
 * Provides a block to filter the media search.
 *
 * @Block(
 *   id = "degov_search_content_full_text_search_block",
 *   admin_label = @Translation("DeGov full text search block")
 * )
 */
class DegovSearchFulltextForm extends BlockBase {

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowed();
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $view = Views::getView('search_content');
    $view->setDisplay('page_1');
    $view->initHandlers();
    $form_state = (new FormState())
      ->setStorage([
        'view' => $view,
        'display' => &$view->display_handler->display,
        'rerender' => TRUE,
      ])
      ->setMethod('get')
      ->setAlwaysProcess()
      ->disableRedirect();
    $form_state->set('rerender', NULL);
    $form = \Drupal::formBuilder()
      ->buildForm(ViewsExposedForm::class, $form_state);
    $form['#theme'] = ['views_exposed_form'];
    return $form;
  }

}
