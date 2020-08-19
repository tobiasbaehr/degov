<?php

declare(strict_types=1);

namespace Drupal\degov_paragraph_view_reference\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Views;

/**
 * Class ViewsSettingsForm.
 *
 * @package Drupal\degov_paragraph_view_reference
 */
final class ViewsSettingsForm extends ConfigFormBase {


  const CONFIG_NAME = 'degov_paragraph_view_reference.views_helper_settings';

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      self::CONFIG_NAME,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'degov_views_helper_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(self::CONFIG_NAME);
    $form['allowed_views'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Allowed View Options'),
      '#options' => $this->getAllViewsNames(),
      '#default_value' => $config->get('allowed_views'),
      '#weight' => 2,
    ];

    $form['form_ids'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Form Ids to apply the filter'),
      '#default_value' => \implode(PHP_EOL, $config->get('form_ids')),
      '#weight' => 3,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->configFactory->getEditable('degov_paragraph_view_reference.views_helper_settings');
    $config->set('allowed_views', array_filter($form_state->getValue('allowed_views')));
    $form_ids = $form_state->getValue('form_ids');
    if ($form_ids !== '') {
      $form_ids = \explode(PHP_EOL, $form_ids);
    }
    else {
      $form_ids = [];
    }
    foreach ($form_ids as $key => $value) {
      $form_ids[$key] = \trim($value);
    }
    $config->set('form_ids', $form_ids)->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * Helper function to get all View Names.
   */
  private function getAllViewsNames() {
    $views = Views::getEnabledViews();
    $options = [];
    foreach ($views as $view) {
      $options[$view->get('id')] = $view->get('label');
    }
    return $options;
  }

}
